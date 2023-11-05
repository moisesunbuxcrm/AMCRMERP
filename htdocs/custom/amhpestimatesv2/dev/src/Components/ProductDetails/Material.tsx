import { useEffect, useState } from 'react';
import '../../Styles/main.css';
import { Layout, Row, Col, Typography, InputNumber, Input, Divider, Button, Image, notification } from 'antd';
import { CloseOutlined, SaveOutlined } from '@ant-design/icons';
import { createItemTotals, DefaultMaterial, Material } from '../../store/Material';
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
import MaterialSearch from '../ModalOptions/MaterialSearch';

const { Content } = Layout;
const { Title, Text } = Typography;

type MaterialProps = {
    estimate?:Estimate
    item?:Material,
    onClose?: () => void
    readOnly: boolean
}

export type ProviderName = {
    value: string
    label: string
}

const createInitialProductCopy = (item:Material|undefined):Material => {
    return item ? { ...item } : { ...DefaultMaterial }
}

const MaterialDetail = ({estimate, item, onClose, readOnly}:MaterialProps) => {
    const products: ps.ProductSummary[] | undefined = useAppSelector(ps.selectProductSummaries)
    const productsByRef: { [key: string]: ps.ProductSummary } | undefined = useAppSelector(ps.selectProductSummariesByRef)
    const defaults: ds.DefaultData | undefined = useAppSelector(ds.selectDefaults)
    const dispatch = useAppDispatch()
    const [material, setMaterial] = useState<Material>(() => createInitialProductCopy(item))
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
        saveAndClose(material)
    }

    const saveAndClose = (p:Material|null) => {
        if (p)
            updateOrSave(p)
       onClose && onClose();
    }

    const updateOrSave = async (p:Material) => {
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

    if (material && estimate) {
        const t = createItemTotals(estimate, material, estimate?.customer)
        if (material.sales_price !== t.sales_price || material.finalprice !== t.finalprice)
            setMaterial({
                ...material,
                sales_price: t.sales_price,
                finalprice: t.finalprice
            })
    }

    // The reference dropdown contains a list of the product summaries 
    // It should show the product used to originally fill in this Material
    // If we change it we must update the associated properties in this material
    const updateRef = (id:number) => {
        setShowProductSearch(false)
        dispatch(ps.fetchProductDetailsById(id)).then((details:any) => {
            let p:ps.ProductDetails=details.payload
   
            let newM:Material = {
                ...material,
                _modified: isModified(material._modified) ? material._modified : ModifiedState.modified,
                color: p.color,
                cost_price: p.cost_price || 0,
                height: p.height,
                heighttxt: p.heighttxt||undefined,
                image: p.image,
                inst_price: p.inst_price || 0,
                itemtype: id2ItemType(p.itemtype),
                length: p.length,
                lengthtxt: p.lengthtxt||undefined,
                modtype: p.modtype || ModuleType.Material,
                name: p.name,
                product_ref: p.ref,
                provider: p.provider,
                sales_price: p.sales_price || 0,
                width: p.width,
                widthtxt: p.widthtxt || undefined,
                wintype: p.wintype,
            }
            updateMaterial(newM)
        })
    }

    const updateMaterial = (newM:Material) => {
        setMaterial({
            ...newM,
            _modified: isNew(newM._modified) ? ModifiedState.new : ModifiedState.modified
        });
    }

    return (
        <Layout className='layout'>
            <Content>
                <Title level={2} style={{ color: '#32445e' }}>Material</Title>
                <Row gutter={[24, 24]}>
                    <Col xs={24} sm={24} md={12} lg={12} xl={12}>
                        <Title level={5}>Reference Product</Title>
                        <Button onClick={() => setShowProductSearch(true)} disabled={readOnly}>{material.product_ref || "Choose..."}</Button>

                        <Divider />
                        <Title level={5}>Provider</Title>
                        <Input className='input show4' placeholder='Provider' disabled={true} value={undefinedOrNone(material.provider)?"None":material.provider}/>

                        <Title level={5}>Type of Opening</Title>
                        <Input className='input show4' placeholder='Type of Opening' disabled={true} value={material.itemtype||""}/>

                        <Title level={5}>Type</Title>
                        <Input className='input show4' placeholder='Type' disabled={true} value={material.wintype}/>

                        <Title level={5}>Color</Title>
                        <Input className='input show1' placeholder='Frame' disabled={true} value={material.color} />

                        <div className='flex_container_center block_space'>
                            <Image.PreviewGroup>
                                <Image hidden={material.image === undefined || material.image === ""}
                                    className='img' src={(process.env.REACT_APP_URL_PREFIX||"") + material.image} alt={material.name}
                                />
                            </Image.PreviewGroup>
                        </div>

                    </Col>

                    <Col xs={24} sm={24} md={12} lg={12} xl={12}>

                        <Title level={5}>Sizes</Title>
                        <div className='flex_container block_space show4'>
                            <Text>W:</Text>
                            <Input hidden={true} value={material.width}/>
                            <Input style={{ marginLeft: 5, marginRight: 15 }} value={material.widthtxt} disabled={true} />
                            <Text>H:</Text>
                            <Input hidden={true} value={material.height}/>
                            <Input style={{ marginLeft: 5, marginRight: 15 }} value={material.heighttxt} disabled={true} />
                            <Text>L:</Text>
                            <Input hidden={true} value={material.length}/>
                            <Input style={{ marginLeft: 5 }} value={material.lengthtxt} disabled={true} />
                        </div>

                        <Divider />

                        <Title level={5}>Sales Price &amp; Discount %</Title>
                        <div className='flex_container block_space'>
                            <InputNumber className='input show4' placeholder='Price' style={{ marginRight: 20 }} disabled={true} 
                                value={material.sales_price}
                                formatter={asMoney}
                            />
                            <InputNumber className='input' placeholder='Discount' min={0} max={1} step={.01}
                                onBlur={e => updateMaterial({...material,sales_discount:percentageParser(e.target.value)})}
                                onStep={(val:number) => updateMaterial({...material,sales_discount:val})}
                                formatter={asPercentage}
                                parser={percentageParser}
                                value={material.sales_discount} 
                                disabled={readOnly} />
                        </div>

                        <div className='flex_container block_space show4'>
                            <Text>Quantity:</Text>
                            <InputNumber min={1} style={{ marginLeft: 5 }} 
                                onBlur={e => updateMaterial({...material,quantity:Number(e.target.value)})}
                                onStep={(val:number) => updateMaterial({...material,quantity:val})}
                                value={material.quantity} 
                                disabled={readOnly} />
                        </div>

                        <Title level={5}>Final Price: {asMoney(material.finalprice)}</Title>

                        <div className='flex_container_end show4'>
                            <Button type={material&&isModified(material._modified)?"primary":"ghost"} shape="round" icon={<SaveOutlined />} size='large' onClick={() => onClickSave()} disabled={readOnly || material.product_ref === undefined}>{item?"Save":"Add"}</Button>
                            <Button type="primary" shape="round" icon={<CloseOutlined />} size='large' onClick={() => onCancel()} style={{ marginLeft: "20px" }}>Cancel</Button>
                        </div>
                    </Col>
                </Row>
            </Content>
            <MaterialSearch 
                visible={showProductSearch} 
                currentID={ref2ProductSummary(material.product_ref)?.id} 
                color={material.color||""} 

                handleCancel={() => setShowProductSearch(false)} 
                handleProduct={updateRef} 
            />
        </Layout>
    );
};

export default MaterialDetail;