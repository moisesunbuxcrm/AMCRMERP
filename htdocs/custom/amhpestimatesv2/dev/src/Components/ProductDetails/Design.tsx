import { useEffect, useState } from 'react';
import '../../Styles/main.css';
import { Layout, Row, Col, Typography, InputNumber, Input, Divider, Button, Image, notification } from 'antd';
import { CloseOutlined, SaveOutlined } from '@ant-design/icons';
import { createItemTotals, DefaultDesign, Design } from '../../store/Design';
import * as ds from '../../store/defaultsSlice';
import * as ps from '../../store/productsSlice';
import * as es from '../../store/estimatesSlice';
import { useAppDispatch, useAppSelector } from '../../store/hooks';
import { isModified, isNew, ModifiedState } from '../../types/Status';
import { Estimate } from '../../store/Estimate';
import { asMoney, asPercentage, percentageParser } from '../../types/formats';
import {id2ItemType} from '../../types/ItemType';
import useModifiedAlert from '../../effects/useModifiedAlert';
import ModuleType from '../../types/ModuleType';
import store from '../../store/store';
import DesignSearch from '../ModalOptions/DesignSearch';

const { Content } = Layout;
const { Title, Text } = Typography;

type DesignProps = {
    estimate?:Estimate
    item?:Design,
    onClose?: () => void
    readOnly: boolean
}

export type ProviderName = {
    value: string
    label: string
}

const createInitialProductCopy = (item:Design|undefined):Design => {
    return item ? { ...item } : { ...DefaultDesign }
}

const DesignDetail = ({estimate, item, onClose, readOnly}:DesignProps) => {
    const products: ps.ProductSummary[] | undefined = useAppSelector(ps.selectProductSummaries)
    const productsByRef: { [key: string]: ps.ProductSummary } | undefined = useAppSelector(ps.selectProductSummariesByRef)
    const defaults: ds.DefaultData | undefined = useAppSelector(ds.selectDefaults)
    const dispatch = useAppDispatch()
    const [design, setDesign] = useState<Design>(() => createInitialProductCopy(item))
    const [showProductSearch, setShowProductSearch] = useState<boolean>(false)

    useEffect(() => {
        if (defaults?.colors.length === 0)
            dispatch(ds.fetchDefaults())
    }, [dispatch, defaults])

    useEffect(() => {
        if (products === undefined || products.length === 0)
            dispatch(ps.fetchProductSummaries())
    }, [dispatch, products])

    useModifiedAlert(estimate, item)

    const onClickSave = () => {
        saveAndClose(design)
    }

    const saveAndClose = (p:Design|null) => {
        if (p)
            updateOrSave(p)
       onClose && onClose();
    }

    const updateOrSave = async (p:Design) => {
        if (isNew(p._modified) && p.id === 0)
            await dispatch(es.addItem(p))
        else
            await dispatch(es.updateItem(p))

        let e:Estimate|undefined = store.getState().estimates.estimate
        if (e && e.id) {
            dispatch(es.saveEstimate(e)).then((res:any)=>  notification.success({
                message: "Save Estimate",
                description: "Success!",
                duration: 10
            }))
        }
    }
    const onCancel = () => {
        onClose && onClose();
    }

    const undefinedOrNone = (o:any) => o === undefined || o === "Undefined" || o === ""
    const ref2ProductSummary = (ref:string|undefined):ps.ProductSummary|undefined => (productsByRef&&ref) ? productsByRef[ref] : undefined

    if (design && estimate) {
        const t = createItemTotals(estimate, design, estimate?.customer)
        if (design.sales_price !== t.sales_price || design.finalprice !== t.finalprice)
            setDesign({
                ...design,
                sales_price: t.sales_price,
                finalprice: t.finalprice
            })
    }

    // The reference dropdown contains a list of the product summaries 
    // It should show the product used to originally fill in this Design
    // If we change it we must update the associated properties in this design
    const updateRef = (id:number) => {
        setShowProductSearch(false)
        dispatch(ps.fetchProductDetailsById(id)).then((details:any) => {
            let p:ps.ProductDetails=details.payload
   
            let newM:Design = {
                ...design,
                _modified: isModified(design._modified) ? design._modified : ModifiedState.modified,
                color: p.color,
                cost_price: p.cost_price || 0,
                height: p.height,
                heighttxt: p.heighttxt||undefined,
                image: p.image,
                inst_price: p.inst_price || 0,
                itemtype: id2ItemType(p.itemtype),
                modtype: p.modtype || ModuleType.Design,
                name: p.name,
                product_ref: p.ref,
                provider: p.provider,
                sales_price: p.sales_price || 0,
                width: p.width,
                widthtxt: p.widthtxt || undefined,
                wintype: p.wintype,
            }
            updateDesign(newM)
        })
    }

    const updateDesign = (newM:Design) => {
        setDesign({
            ...newM,
            _modified: isNew(newM._modified) ? ModifiedState.new : ModifiedState.modified
        });
    }

    return (
        <Layout className='layout'>
            <Content>
                <Title level={2} style={{ color: '#32445e' }}>Design</Title>
                <Row gutter={[24, 24]}>
                    <Col xs={24} sm={24} md={12} lg={12} xl={12}>
                        <Title level={5}>Reference Product</Title>
                        <Button onClick={() => setShowProductSearch(true)} disabled={readOnly}>{design.product_ref || "Choose..."}</Button>

                        <Divider />
                        <Title level={5}>Provider</Title>
                        <Input className='input show4' placeholder='Provider' disabled={true} value={undefinedOrNone(design.provider)?"None":design.provider}/>

                        <Title level={5}>Type of Opening</Title>
                        <Input className='input show4' placeholder='Type of Opening' disabled={true} value={design.itemtype||""}/>

                        <Title level={5}>Type</Title>
                        <Input className='input show4' placeholder='Type' disabled={true} value={design.wintype}/>

                        <Title level={5}>Color</Title>
                        <Input className='input show1' placeholder='Frame' disabled={true} value={design.color} />

                        <div className='flex_container_center block_space'>
                            <Image.PreviewGroup>
                                <Image hidden={design.image === undefined || design.image === ""}
                                    className='img' src={(process.env.REACT_APP_URL_PREFIX||"") + design.image} alt={design.name}
                                />
                            </Image.PreviewGroup>
                        </div>

                    </Col>

                    <Col xs={24} sm={24} md={12} lg={12} xl={12}>

                        <Title level={5}>Sizes</Title>
                        <div className='flex_container block_space show4'>
                            <Text>W:</Text>
                            <Input hidden={true} value={design.width}/>
                            <Input style={{ marginLeft: 5, marginRight: 15 }} value={design.widthtxt} disabled={true} />
                            <Text>H:</Text>
                            <Input hidden={true} value={design.height}/>
                            <Input style={{ marginLeft: 5, marginRight: 15 }} value={design.heighttxt} disabled={true} />
                        </div>

                        <Divider />

                        <Title level={5}>Sales Price &amp; Discount %</Title>
                        <div className='flex_container block_space'>
                            <InputNumber className='input show4' placeholder='Price' style={{ marginRight: 20 }} disabled={true} 
                                value={design.sales_price}
                                formatter={asMoney}
                            />
                            <InputNumber className='input' placeholder='Discount' min={0} max={1} step={.01}
                                onBlur={e => updateDesign({...design,sales_discount:percentageParser(e.target.value)})}
                                onStep={(val:number) => updateDesign({...design,sales_discount:val})}
                                formatter={asPercentage}
                                parser={percentageParser}
                                value={design.sales_discount} 
                                disabled={readOnly} />
                        </div>

                        <Title level={5}>Final Price: {asMoney(design.finalprice)}</Title>

                        <div className='flex_container_end show4'>
                            <Button type={design&&isModified(design._modified)?"primary":"ghost"} shape="round" icon={<SaveOutlined />} size='large' onClick={() => onClickSave()} disabled={readOnly || design.product_ref === undefined}>{item?"Save":"Add"}</Button>
                            <Button type="primary" shape="round" icon={<CloseOutlined />} size='large' onClick={() => onCancel()} style={{ marginLeft: "20px" }}>Cancel</Button>
                        </div>
                    </Col>
                </Row>
            </Content>
            <DesignSearch 
                visible={showProductSearch} 
                currentID={ref2ProductSummary(design.product_ref)?.id} 
                color={design.color||""} 

                handleCancel={() => setShowProductSearch(false)} 
                handleProduct={updateRef} 
            />
        </Layout>
    );
};

export default DesignDetail;