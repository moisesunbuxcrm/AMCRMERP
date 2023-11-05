import { useEffect, useState } from 'react';
import '../../Styles/main.css';
import { CloseOutlined, SaveOutlined } from '@ant-design/icons';
import { Layout, Row, Col, Typography, Select, Input, InputNumber, Divider, Button, Popconfirm, Image, notification } from 'antd';
import { createItemTotals, DefaultHardware, Hardware } from '../../store/Hardware';
import * as ds from '../../store/defaultsSlice';
import * as ps from '../../store/productsSlice';
import * as es from '../../store/estimatesSlice';
import { useAppDispatch, useAppSelector } from '../../store/hooks';
import { WarningOutlined } from '@ant-design/icons';
import { isModified, isNew, LoadingState, ModifiedState } from '../../types/Status';
import { useHistory } from 'react-router';
import { Estimate } from '../../store/Estimate';
import { asMoney, asPercentage, percentageParser } from '../../types/formats';
import useModifiedAlert from '../../effects/useModifiedAlert';
import ModuleType from '../../types/ModuleType';
import store from '../../store/store';

const { Content } = Layout;
const { Title } = Typography;
const { Option } = Select;

type HardwareProps = {
    estimate?:Estimate
    item?:Hardware
    onClose?: () => void
    readOnly: boolean
}

const createInitialProductCopy = (item:Hardware|undefined):Hardware => {
    return item ? { ...item } : { ...DefaultHardware }
}

const HardwareDetail = ({estimate, item, onClose, readOnly}:HardwareProps) => {
    const products: ps.ProductSummary[] | undefined = useAppSelector(ps.selectProductSummaries)
    const productsById: { [key: number]: ps.ProductSummary } | undefined = useAppSelector(ps.selectProductSummariesById)
    const productsByRef: { [key: string]: ps.ProductSummary } | undefined = useAppSelector(ps.selectProductSummariesByRef)
    const productsStatus = useAppSelector(ps.selectProductSummariesStatus)
    const defaults: ds.DefaultData | undefined = useAppSelector(ds.selectDefaults)
    const defaultStatus = useAppSelector(ds.selectDefaultsStatus)
    const dispatch = useAppDispatch();
    const history = useHistory();
    const [hardware, setHardware] = useState<Hardware>(() => createInitialProductCopy(item))

    useEffect(() => {
        if (defaults?.colors.length === 0)
            dispatch(ds.fetchDefaults())
    }, [dispatch, defaults])

    useEffect(() => {
        if (products === undefined || products.length === 0)
            dispatch(ps.fetchProductSummaries())
    }, [dispatch, products])

    useModifiedAlert(estimate, item)

    const handleSubmit = () => {
        history.goBack()
    }

    const onSave = async () => {
        if (isNew(hardware._modified) && hardware.id === 0)
            await dispatch(es.addItem(hardware))
        else
            await dispatch(es.updateItem(hardware))
            
        let e:Estimate|undefined = store.getState().estimates.estimate
        if (e && e.id) {
            dispatch(es.saveEstimate(e)).then((res:any)=>  notification.success({
                message: "Save Estimate",
                description: "Success!",
                duration: 10
            }))
        }
        onClose && onClose();
    }

    const onCancel = () => {
        onClose && onClose();
    }

    const product2Label = (p:ps.ProductSummary|undefined):string => {
        if (p)
            return p.id + " - " + p.name + " - " + p.ref
        return "None"
    }
    const undefinedOrNone = (o:any) => o === undefined || o === "None" || o === ""
    const id2ProductSummary = (id:number):ps.ProductSummary|undefined => productsById ? productsById[id] : undefined
    const ref2ProductSummary = (ref:string|undefined):ps.ProductSummary|undefined => (productsByRef&&ref) ? productsByRef[ref] : undefined
    const filterOption = (input: any, option: any) => {
        const v = option.value.toLowerCase()
        const i = input.toLowerCase()
        return i==="" || (v !== null && (v?.indexOf(i) >= 0 || v.indexOf(i) >= 0))
    }
    const isVisibleProductSummary = (p:ps.ProductSummary) => {
        let visible:boolean = undefinedOrNone(hardware.provider) || p.provider === hardware.provider
        visible = visible && p.modtype === ModuleType.Hardware
        return visible
    } 
    const filterReferencesOptionsOnKeyPress = (input: any, option: any) => {
        let p = undefinedOrNone(option.key)?undefined:id2ProductSummary(option.key)
        let match:boolean = p!==undefined && isVisibleProductSummary(p)
        match = match && filterOption(input, option)
        return match
    }

    if (hardware && estimate) {
        const t = createItemTotals(estimate, hardware, estimate?.customer)
        if (hardware.sales_price !== t.sales_price || hardware.finalprice !== t.finalprice)
            setHardware({
                ...hardware,
                sales_price: t.sales_price,
                finalprice: t.finalprice
            })
    }

    // The reference dropdown contains a list of the product summaries 
    // It should show the product used to originally fill in this Hardware
    // If we change it we must update the associated properties in this hardware
    const updateRef = (id:number) => {
        dispatch(ps.fetchProductDetailsById(id)).then((details:any) => {
            let p:ps.ProductDetails=details.payload
            let newIP:Hardware = {
                ...hardware,
                _modified: isModified(hardware._modified) ? hardware._modified : ModifiedState.modified,
                color: p.color,
                image: p.image,
                inst_price: p.inst_price || 0,
                name: p.name,
                product_ref: p.ref,
                provider: p.provider,
                cost_price: p.cost_price || 0,
                sales_price: p.sales_price || 0,
                configuration: p.configuration,
                hardwaretype: p.hardwaretype
            }
            updateHardware(newIP)
        })
    }

    const updateHardware = (newHW:Hardware) => {
        setHardware({
            ...newHW,
            _modified: isNew(newHW._modified) ? ModifiedState.new : ModifiedState.modified
        });
    }

    return (
        <Layout className='layout'>
            <Content>
                <Title level={2} style={{ color: '#32445e' }}>Hardware</Title>
                <Row gutter={[24, 24]}>
                    <Col xs={24} sm={24} md={12} lg={12} xl={12}>
                        <Title level={5}>Provider</Title>
                        <Select
                            showSearch
                            className='input show1'
                            optionFilterProp="label"
                            onChange={(txt:string,p:any) => updateHardware({...hardware,provider: p.value})}
                            filterOption={filterOption}
                            value={undefinedOrNone(hardware.provider)?"None":hardware.provider}
                            loading ={defaultStatus===LoadingState.loading}
                            placeholder="Select Provider..."
                            disabled={readOnly}
                            >
                                <Option value="None" key="None">None</Option>
                                {defaults?.providers.map(p => <Option value={p.label} key={p.value}>{p.label}</Option>)}
                            </Select>
                        {defaultStatus===LoadingState.failed ? <WarningOutlined style={{ marginLeft: "1rem", display: "flex", alignSelf: "center" }} /> : null}

                        <Title level={5}>Reference</Title>
                        <Select
                            showSearch
                            className='input show1'
                            optionFilterProp="ref"
                            onChange={(input:string,p:any) => p&&updateRef(Number(p.key))}
                            filterOption={filterReferencesOptionsOnKeyPress}
                            value={product2Label(ref2ProductSummary(hardware.product_ref))}
                            loading ={productsStatus===LoadingState.loading}
                            placeholder="Select Product Reference..."
                            disabled={readOnly}
                        >
                            <Option value="None" key="0">None</Option>
                            {products?.filter(isVisibleProductSummary).map(p => <Option value={product2Label(p)} key={p.id}>{product2Label(p)}</Option>)}
                        </Select>

                        <Divider />

                        <Title level={5}>Hardware Type</Title>
                        <Input className='input show4' placeholder='Hardware Type' disabled={true}  value={hardware?.hardwaretype} />

                        <Title level={5}>Configuration</Title>
                        <Input className='input show3' placeholder='Configuration' disabled={true} value={hardware?.configuration}/>

                        <Title level={5}>Color</Title>
                        <Input className='input show1' placeholder='Color' disabled={true} value={hardware?.color} />
                    </Col>

                    <Col xs={24} sm={24} md={12} lg={12} xl={12}>

                        {estimate?.is_installation_included ?
                            <>
                                <Title level={5}>Installation &amp; Discount %</Title>
                                <div className='flex_container block_space show1'>
                                    <InputNumber className='input' placeholder='Installation' style={{ marginRight: 20 }} disabled={true}
                                        formatter={asMoney}
                                        value={hardware.inst_price}
                                    />
                                    <InputNumber className='input' placeholder='Discount' min={0} max={1} step={.01} 
                                        onBlur={e => updateHardware({...hardware,inst_discount:percentageParser(e.target.value)})}
                                        onStep={(val:number) => updateHardware({...hardware,inst_discount:val})}
                                        formatter={asPercentage}
                                        parser={percentageParser}
                                        value={hardware.inst_discount} 
                                        disabled={readOnly}
                                        />
                                </div>
                            </>:""
                        }

                        <Title level={5}>Sales Price &amp; Discount %</Title>
                        <div className='flex_container block_space'>
                            <InputNumber className='input show4' placeholder='Price' style={{ marginRight: 20 }} disabled={true} 
                                formatter={asMoney}
                                value={hardware.sales_price}
                            />
                            <InputNumber className='input' placeholder='Discount' min={0} max={1} step={.01} 
                                onBlur={e => updateHardware({...hardware,sales_discount:percentageParser(e.target.value)})}
                                onStep={(val:number) => updateHardware({...hardware,sales_discount:val})}
                                formatter={asPercentage}
                                parser={percentageParser}
                                value={hardware.sales_discount} 
                                disabled={readOnly}
                                />
                        </div>

                        <Title level={5}>Final Price: {asMoney(hardware.finalprice)}</Title>

                        <Divider />
                        <div className='flex_container_center block_space'>
                            <Image.PreviewGroup>
                                <Image hidden={hardware.image === undefined || hardware.image === ""}
                                    className='img' src={(process.env.REACT_APP_URL_PREFIX||"") + hardware.image} alt={hardware.name}
                                />
                            </Image.PreviewGroup>
                        </div>

                    </Col>
                    <Col xs={24} sm={24} md={12} lg={12} xl={12}>
                    </Col>
                    <Col xs={24} sm={24} md={12} lg={12} xl={12}>
                        <div className='flex_container_center show4'>
                            <Popconfirm placement="top" title='Do you want to include a hardware?' onConfirm={() => handleSubmit()} okText="Yes" cancelText="No">
                                <Button type={hardware&&isModified(hardware._modified)?"primary":"ghost"} shape="round" icon={<SaveOutlined />} size='large' onClick={() => onSave()} disabled={readOnly || hardware.product_ref === undefined}>{hardware?"Save":"Add"}</Button>
                                <Button type="primary" shape="round" icon={<CloseOutlined />} size='large' onClick={() => onCancel()} style={{ marginLeft: "20px" }}>Cancel</Button>
                            </Popconfirm>
                        </div>
                    </Col>
                </Row>
            </Content>
        </Layout>
    );
};

export default HardwareDetail;