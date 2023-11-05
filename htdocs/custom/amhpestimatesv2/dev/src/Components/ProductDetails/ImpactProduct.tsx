import { useEffect, useState } from 'react';
import '../../Styles/main.css';
import { Layout, Row, Col, Typography, Select, Checkbox, InputNumber, Input, Divider, Button, Tooltip, Image, Menu, Dropdown, Space, notification } from 'antd';
import { CloseOutlined, DownOutlined, InfoCircleOutlined, SaveOutlined } from '@ant-design/icons';
import RoomOptions, { RoomType } from '../../Components/ModalOptions/RoomOptions';
import { createItemTotals, DefaultImpactProduct, ImpactProduct } from '../../store/ImpactProduct';
import * as ds from '../../store/defaultsSlice';
import * as ps from '../../store/productsSlice';
import * as es from '../../store/estimatesSlice';
import { useAppDispatch, useAppSelector } from '../../store/hooks';
import { isModified, isNew, LoadingState, ModifiedState } from '../../types/Status';
import { Estimate } from '../../store/Estimate';
import { asMoney, asPercentage, moneyParser, percentageParser } from '../../types/formats';
import {id2ItemType} from '../../types/ItemType';
import AddHardwareConfirmation from '../ModalOptions/AddHardwareConfirmation';
import WinType from '../../types/WinType';
import useModifiedAlert from '../../effects/useModifiedAlert';
import ModuleType from '../../types/ModuleType';
import MenuItem from 'antd/lib/menu/MenuItem';
import TextArea from 'rc-textarea';
import ProductSearch from '../ModalOptions/ProductSearch';
import store from '../../store/store';

const { Content } = Layout;
const { Title, Text } = Typography;
const { Option } = Select;

type ImpactProductProps = {
    estimate?:Estimate
    item?:ImpactProduct,
    onClose?: (addHardware:boolean) => void
    readOnly: boolean
}

export type ProviderName = {
    value: string
    label: string
}

const createInitialProductCopy = (item:ImpactProduct|undefined):ImpactProduct => {
    return item ? { ...item } : { ...DefaultImpactProduct }
}

const ImpactProductDetail = ({estimate, item, onClose, readOnly}:ImpactProductProps) => {
    const [showRoomOptions, setShowRoomOptions] = useState<boolean>(false)
    const [showAddHardwareConfirmation, setShowAddHardwareConfirmation] = useState<boolean>(false)
    const products: ps.ProductSummary[] | undefined = useAppSelector(ps.selectProductSummaries)
    const productsByRef: { [key: string]: ps.ProductSummary } | undefined = useAppSelector(ps.selectProductSummariesByRef)
    const defaults: ds.DefaultData | undefined = useAppSelector(ds.selectDefaults)
    const defaultStatus = useAppSelector(ds.selectDefaultsStatus)
    const dispatch = useAppDispatch()
    const [impactProduct, setImpactProduct] = useState<ImpactProduct>(() => createInitialProductCopy(item))
    const id2RoomType:ds.RoomTypeConverterFunction = useAppSelector(ds.selectId2RoomTypeConvertor)
    const [editRoomDescription, setEditRoomDescription] = useState(false)
    const [showProductSearch, setShowProductSearch] = useState<boolean>(false)

    useEffect(() => {
        if (defaults?.colors.length === 0)
            dispatch(ds.fetchDefaults())
    }, [dispatch, defaults])

    useEffect(() => {
        if (impactProduct.roomtype === undefined)
            setImpactProduct({
                ...impactProduct,
                roomtype: 0
            })
    }, [impactProduct])

    useEffect(() => {
        if (products === undefined || products.length === 0)
            dispatch(ps.fetchProductSummaries())
    }, [dispatch, products])

    useModifiedAlert(estimate, item)

    const onClickSave = () => {
        if (impactProduct.roomtype === 0)
            setShowRoomOptions(true);
        else if (impactProduct.wintype === WinType.FrenchDoor)
            setShowAddHardwareConfirmation(true);
        else {
            saveAndClose(impactProduct, false)
        }
    }

    const saveAndClose = (p:ImpactProduct|null, addHardware:boolean) => {
        if (p)
            updateOrSave(p)
        onClose && onClose(addHardware);
    }

    const updateOrSave = async (p:ImpactProduct) => {
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

    const onSaveRoom = (type:RoomType) => {
        setShowRoomOptions(false);
        if (impactProduct.wintype === WinType.FrenchDoor) {
            updateImpactProduct({
                ...impactProduct,
                roomtype: type
            })
            setShowAddHardwareConfirmation(true);
        }
        else {
            // We need to save the item to the store now because the editor is closing
            updateOrSave({
                ...impactProduct,
                roomtype: type
            })
            saveAndClose(null, false)
        }
    }

    const onCancelRoom = () => {
        setShowRoomOptions(false);
    }

    const onCancelAddHardwareConfirmation = () => {
        setShowAddHardwareConfirmation(false);
    }

    const onCancel = () => {
        onClose && onClose(false);
    }

    const undefinedOrNone = (o:any) => o === undefined || o === "Undefined" || o === ""
    const ref2ProductSummary = (ref:string|undefined):ps.ProductSummary|undefined => (productsByRef&&ref) ? productsByRef[ref] : undefined
    const filterOption = (input: any, option: any) => {
        const v = option.value.toLowerCase()
        const i = input.toLowerCase()
        return i==="" || (v !== null && (v?.indexOf(i) >= 0 || v.indexOf(i) >= 0))
    }

    if (impactProduct && estimate) {
        const t = createItemTotals(estimate, impactProduct, estimate?.customer)
        if (impactProduct.sales_price !== t.sales_price || impactProduct.finalprice !== t.finalprice)
            setImpactProduct({
                ...impactProduct,
                sales_price: t.sales_price,
                finalprice: t.finalprice
            })
    }

    // The reference dropdown contains a list of the product summaries 
    // It should show the product used to originally fill in this Impact Product
    // If we change it we must update the associated properties in this impact product
    const updateRef = (id:number) => {
        setShowProductSearch(false)
        dispatch(ps.fetchProductDetailsById(id)).then((details:any) => {
            let p:ps.ProductDetails=details.payload
   
            let newIP:ImpactProduct = {
                ...impactProduct,
                _modified: isModified(impactProduct._modified) ? impactProduct._modified : ModifiedState.modified,
                coating: p.coating,
                configuration: p.configuration,
                color: p.color,
                cost_price: p.cost_price || 0,
                frame_color: p.frame_color,
                glass_color: p.glass_color,
                glass_type: p.glass_type,
                height: p.height,
                heighttxt: p.heighttxt||undefined,
                image: p.image,
                inst_price: p.inst_price || 0,
                interlayer: p.interlayer,
                is_screen: ![WinType.FrenchDoor, WinType.FixedGlass].includes(p.wintype),
                itemtype: id2ItemType(p.itemtype),
                length: p.length,
                lengthtxt: p.lengthtxt||undefined,
                modtype: p.modtype || ModuleType.ImpactProduct,
                name: p.name,
                product_ref: p.ref,
                provider: p.provider,
                sales_price: p.sales_price || 0,
                width: p.width,
                widthtxt: p.widthtxt || undefined,
                wintype: p.wintype,
            }
            updateImpactProduct(newIP)
        })
    }

    const updateImpactProduct = (newIP:ImpactProduct) => {
        setImpactProduct({
            ...newIP,
            _modified: isNew(newIP._modified) ? ModifiedState.new : ModifiedState.modified
        });
    }

    const existingRoomDescriptions:string[] = []
    estimate?.items?.forEach(i => {
        if (i.modtype === ModuleType.ImpactProduct) {
            const ip = i as ImpactProduct
            if (ip.room_description && !existingRoomDescriptions.includes(ip.room_description))
                existingRoomDescriptions.push(ip.room_description)
        }
    })
    if (impactProduct.room_description && !existingRoomDescriptions.includes(impactProduct.room_description))
        existingRoomDescriptions.push(impactProduct.room_description)
    existingRoomDescriptions.sort((a,b) => a.localeCompare(b))

    const roomDescriptionEditor = 
            <Menu>
                {existingRoomDescriptions.map((d,i) => <MenuItem key={i} onClick={() => updateImpactProduct({...impactProduct,room_description: d})}>{d}</MenuItem>)}
                <MenuItem key={existingRoomDescriptions.length} onClick={() => setEditRoomDescription(true)}>Click here to write new description</MenuItem>
            </Menu>
    
    return (
        <Layout className='layout'>
            <Content>
                <Title level={2} style={{ color: '#32445e' }}>Impact Product</Title>
                <Row gutter={[24, 24]}>
                    <Col xs={24} sm={24} md={12} lg={12} xl={12}>
                        <Title level={5}>Reference Product</Title>
                        <Button onClick={() => setShowProductSearch(true)} disabled={readOnly}>{impactProduct.product_ref || "Choose..."}</Button>

                        <div className='flex_container block_space show3'>
                            <Checkbox
                                checked={impactProduct.is_standard}
                                onChange={() => {}}
                                disabled={true}
                            >
                                <Title level={5}>Standard</Title>
                            </Checkbox>
                            <Checkbox
                                checked={impactProduct.is_def_color}
                                onChange={(e:any) => updateImpactProduct({...impactProduct,is_def_color:e.target.checked})}
                                disabled={readOnly}
                            >
                                <Title level={5}>Def. Frame Color</Title>
                            </Checkbox>
                            <Checkbox
                                checked={impactProduct.is_def_glass_color}
                                onChange={(e:any) => updateImpactProduct({...impactProduct,is_def_glass_color:e.target.checked})}
                                disabled={readOnly}
                            >
                                <Title level={5}>Def. Glass Color</Title>
                            </Checkbox>
                        </div>

                        <div className='flex_container block_space show2'>
                            <Title level={5} style={{ marginRight: 15 }}>Type of Room:</Title>
                            <Select<string>
                                showSearch
                                style={{ flexGrow: 1 }}
                                optionFilterProp="label"
                                onChange={(txt:string,p:any) => updateImpactProduct({...impactProduct,roomtype: p.key})}
                                filterOption={filterOption}
                                loading={defaultStatus===LoadingState.loading}
                                value={id2RoomType(impactProduct?.roomtype?.toString())}
                                disabled={readOnly}
                            >
                                {defaults?.roomtypes.map(rt => <Option value={rt.label} key={rt.value}>{rt.label}</Option>)}
                            </Select>
                        </div>

                        <div className='flex_container block_space show2'>
                            <Title level={5} className='input_text'>Room #</Title>
                            <InputNumber min={1} onChange={(num:number) => updateImpactProduct({...impactProduct,roomnum: num})} value={impactProduct.roomnum||0} disabled={readOnly} />
                            <Title level={5} className='input_text'>Floor #</Title>
                            <InputNumber min={1} onChange={(num:number) => updateImpactProduct({...impactProduct,floornum: num})} value={impactProduct.floornum||0} disabled={readOnly} />
                        </div>

                        <div className='flex_container block_space show2'>
                            <Title level={5}>Description:</Title>
                            {editRoomDescription ?
                                <TextArea autoFocus onFocus={e => e.target.select()} maxLength={100} style={{marginLeft: "2rem", width: "100%"}} defaultValue={impactProduct?.room_description} onBlur={e => { updateImpactProduct({...impactProduct,room_description: e.target.value}); setEditRoomDescription(false)} }></TextArea>
                                : <Dropdown overlay={roomDescriptionEditor} disabled={readOnly}>
                                        <Button type="link" onClick={() => setEditRoomDescription(true)} style={{ marginBottom: ".5em", width: "100%", whiteSpace: "normal" }} >
                                            <Space>
                                                {impactProduct?.room_description || "Click to write a new description"}
                                                <DownOutlined />
                                            </Space>
                                        </Button>
                                  </Dropdown>

                            }
                        </div>

                        <Divider />
                        <Title level={5}>Provider</Title>
                        <Input className='input show4' placeholder='Provider' disabled={true} value={undefinedOrNone(impactProduct.provider)?"None":impactProduct.provider}/>

                        <Title level={5}>Type of Opening</Title>
                        <Input className='input show4' placeholder='Type of Opening' disabled={true} value={impactProduct.itemtype||""}/>

                        <Title level={5}>Type</Title>
                        <Input className='input show4' placeholder='Type' disabled={true} value={impactProduct.wintype}/>

                        <Title level={5}>Configuration</Title>
                        <Input className='input show3' placeholder='Configuration' disabled={true} value={impactProduct.configuration}/>

                        <Title level={5}>Frame Color</Title>
                        <Input className='input show1' placeholder='Frame' disabled={true} value={impactProduct.frame_color} />

                        <div className='flex_container_center block_space'>
                            <Image.PreviewGroup>
                                <Image hidden={impactProduct.image === undefined || impactProduct.image === ""}
                                    className='img' src={(process.env.REACT_APP_URL_PREFIX||"") + impactProduct.image} alt={impactProduct.name}
                                />
                            </Image.PreviewGroup>
                        </div>

                    </Col>

                    <Col xs={24} sm={24} md={12} lg={12} xl={12}>

                        <div className='block_space show2'>
                            <Checkbox
                                checked={impactProduct.is_colonial}
                                onChange={(e:any) => updateImpactProduct({...impactProduct,is_colonial:e.target.checked})}
                                className='input_colonial'
                                disabled={readOnly}
                            >
                                <Title level={5}>Colonial</Title>
                            </Checkbox>
                            <div className={`flex_container ${impactProduct.is_colonial ? 'show1' : 'hide'}`}>
                                <Text>A:</Text>
                                <InputNumber className='input' placeholder='Across' 
                                        style={{ marginLeft: 5, marginRight: 15, width: '25%' }} 
                                        onBlur={e => updateImpactProduct({...impactProduct,colonial_across:Number(e.target.value)})}
                                        onStep={(val:number) => updateImpactProduct({...impactProduct,colonial_across:val})}
                                        value={impactProduct.colonial_across} 
                                        disabled={readOnly} />
                                <Text>D:</Text>
                                <InputNumber className='input' placeholder='Down' 
                                        style={{ marginLeft: 5, width: '25%' }} 
                                        onBlur={e => updateImpactProduct({...impactProduct,colonial_down:Number(e.target.value)})}
                                        onStep={(val:number) => updateImpactProduct({...impactProduct,colonial_down:val})}
                                        value={impactProduct.colonial_down} 
                                        disabled={readOnly} />
                            </div>
                        </div>

                        <div className='flex_container block_space'>
                            <Checkbox
                                checked={impactProduct.is_screen}
                                onChange={(e:any) => updateImpactProduct({...impactProduct,is_screen:e.target.checked})}
                                disabled={readOnly || [WinType.FrenchDoor, WinType.FixedGlass].includes(impactProduct.wintype)}
                            >
                                <Title level={5}>Screen</Title>
                            </Checkbox>
                        </div>

                        <Title level={5}>Sizes</Title>
                        <div className='flex_container block_space show4'>
                            <Text>W:</Text>
                            <Input hidden={true} value={impactProduct.width}/>
                            <Input style={{ marginLeft: 5, marginRight: 15 }} value={impactProduct.widthtxt} disabled={true} />
                            <Text>H:</Text>
                            <Input hidden={true} value={impactProduct.height}/>
                            <Input style={{ marginLeft: 5, marginRight: 15 }} value={impactProduct.heighttxt} disabled={true} />
                            <Text>L:</Text>
                            <Input hidden={true} value={impactProduct.length}/>
                            <Input style={{ marginLeft: 5 }} value={impactProduct.lengthtxt} disabled={true} />
                        </div>

                        <Title level={5}>Glass Type</Title>
                        <Input className='input show1' placeholder="Glass" disabled={true} value={impactProduct.glass_type} />

                        <Title level={5}>Glass Color</Title>
                        <Input className='input show3' placeholder="Glass Color" disabled={true} value={impactProduct.glass_color}/>

                        <Title level={5}>Interlayer</Title>
                        <Input className='input show2' placeholder='Interlayer' disabled={true} value={impactProduct.interlayer}/>

                        <Title level={5}>Coating</Title>
                        <Input className='input show4' placeholder='Coating' disabled={true} value={impactProduct.coating} />

                        <Divider />

                        {estimate?.is_installation_included ?
                            <>
                                <Title level={5}>Installation &amp; Discount %</Title>
                                <div className='flex_container block_space show1'>
                                    <InputNumber className='input' placeholder='Installation' style={{ marginRight: 20 }} disabled={true}
                                            formatter={asMoney}
                                            value={impactProduct.inst_price}
                                        />
                                    <InputNumber className='input' placeholder='Discount' min={0} max={1} step={.01} 
                                        onBlur={e => updateImpactProduct({...impactProduct,inst_discount:percentageParser(e.target.value)})}
                                        onStep={(val:number) => updateImpactProduct({...impactProduct,inst_discount:val})}
                                        formatter={asPercentage}
                                        parser={percentageParser}
                                        value={impactProduct.inst_discount} 
                                        disabled={readOnly} />
                                </div>
                            </>:""
                        }

                        {impactProduct.is_colonial ? 
                            <>
                                <Title level={5}>Colonial Fee <Tooltip title="Colonial is a different material that has an extra cost"><InfoCircleOutlined twoToneColor='#32445E' /></Tooltip></Title>
                                <InputNumber className='input show1' placeholder='Colonial Fee' 
                                    onBlur={e => updateImpactProduct({...impactProduct,colonial_fee:moneyParser(e.target.value)})}
                                    onStep={(val:number) => updateImpactProduct({...impactProduct,colonial_fee:val})}
                                    formatter={asMoney}
                                    parser={moneyParser}
                                    value={impactProduct.colonial_fee} 
                                    disabled={readOnly} />
                            </>
                        : ""}

                        <Title level={5}>Sales Price &amp; Discount %</Title>
                        <div className='flex_container block_space'>
                            <InputNumber className='input show4' placeholder='Price' style={{ marginRight: 20 }} disabled={true} 
                                value={impactProduct.sales_price}
                                formatter={asMoney}
                            />
                            <InputNumber className='input' placeholder='Discount' min={0} max={1} step={.01}
                                onBlur={e => updateImpactProduct({...impactProduct,sales_discount:percentageParser(e.target.value)})}
                                onStep={(val:number) => updateImpactProduct({...impactProduct,sales_discount:val})}
                                formatter={asPercentage}
                                parser={percentageParser}
                                value={impactProduct.sales_discount} 
                                disabled={readOnly} />
                        </div>

                        <Title level={5}>Final Price: {asMoney(impactProduct.finalprice)}</Title>

                        <div className='flex_container_end show4'>
                            <Button type={impactProduct&&isModified(impactProduct._modified)?"primary":"ghost"} shape="round" icon={<SaveOutlined />} size='large' onClick={() => onClickSave()} disabled={readOnly || impactProduct.product_ref === undefined}>{item?"Save":"Add"}</Button>
                            <Button type="primary" shape="round" icon={<CloseOutlined />} size='large' onClick={() => onCancel()} style={{ marginLeft: "20px" }}>Cancel</Button>
                        </div>
                    </Col>
                </Row>
            </Content>
            <RoomOptions isModalVisible={showRoomOptions} handleCancel={() => onCancelRoom()} handleTypeOptions={onSaveRoom} />
            <AddHardwareConfirmation isModalVisible={showAddHardwareConfirmation} onYes={() => saveAndClose(impactProduct, true)} onNo={() => saveAndClose(impactProduct, false)} onCancel={() => onCancelAddHardwareConfirmation()} />
            <ProductSearch 
                visible={showProductSearch} 
                currentID={ref2ProductSummary(impactProduct.product_ref)?.id} 
                color={impactProduct.is_def_color ? (estimate?.defcolor||"") : ""} 
                glasscolor={impactProduct.is_def_glass_color ? (estimate?.defglasscolor||"") : ""} 

                handleCancel={() => setShowProductSearch(false)} 
                handleProduct={updateRef} 
            />
        </Layout>
    );
};

export default ImpactProductDetail;