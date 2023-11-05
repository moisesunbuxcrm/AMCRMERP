import { useCallback, useEffect, useMemo, useState } from 'react';
import '../../Styles/main.css';
import { Typography, Divider, message, Collapse, notification, Row, Col, Button, Spin, Empty, Alert, Space, Tag, Menu, Dropdown } from 'antd';
import { useAppDispatch, useAppSelector } from '../../store/hooks';
import * as cs from '../../store/customersSlice';
import * as es from '../../store/estimatesSlice';
import * as ds from '../../store/defaultsSlice';
import { AppDispatch } from '../../store/store';
import { useHistory, useParams } from "react-router-dom";
import { History, LocationState } from 'history';
import ProductOptions from '../ModalOptions/ProductOptions';
import TotalsTable from '../Estimates/TotalsTable';
import ModuleType from '../../types/ModuleType';
import ImpactProductComponent from '../ProductSummary/ImpactProductComponent';
import HardwareComponent from '../ProductSummary/HardwareComponent';
import { Hardware } from '../../store/Hardware';
import { ImpactProduct } from '../../store/ImpactProduct';
import { EstimateItem } from '../../store/EstimateItem';
import { createEstimateTotals } from '../../store/EstimateTotals';
import { ProductButtons } from '../ProductSummary/ProductComponent';
import { isDeleted, isModified, LoadingState, ModifiedState } from '../../types/Status';
import { createDefaultEstimate, Estimate, formatDate, isDirty } from '../../store/Estimate';
import { AuditOutlined, CopyOutlined, DownOutlined, PlusOutlined, SaveOutlined, SyncOutlined, WarningOutlined } from '@ant-design/icons';
import { EstimateProblem, validateEstimate } from '../../store/validations';
import { asMoney } from "../../types/formats";
import TextArea from 'antd/lib/input/TextArea';
import useModifiedAlert from '../../effects/useModifiedAlert';
import EstimateOptions from '../Estimates/EstimateOptions';
import { EstimateStatus, isNotInProgress, isReadOnly, isRejected, status2color, string2status } from '../../types/EstimateStatus';
import MenuItem from 'antd/lib/menu/MenuItem';
import Initial from './Initial';
import { useAppRoutes } from '../../routes';
import CompactCustomerData from './CompactCustomerData';
import TwoColumnTotalsTable from './TwoColumnTotalsTable';
import { createItemTotals as createMaterialItemTotals, Material } from '../../store/Material';
import { createItemTotals as createDesignItemTotals, Design } from '../../store/Design';
import MaterialComponent from '../ProductSummary/MaterialComponent';
import DesignComponent from '../ProductSummary/DesignComponent';

const { Title, Text } = Typography;

const isLoading = (statuses: any): boolean => {
    if (!isFailed(statuses)) { // False if something failed
        for (const key in statuses) {
            if (statuses[key] === LoadingState.loading)
                return true;
        }
    }
    return false;
}

const isFailed = (statuses: any): boolean => {
    for (const key in statuses) {
        if (statuses[key] === LoadingState.failed)
            return true;
    }
    return false;
}

const isLoaded = (statuses: any): boolean => {
    for (const key in statuses) {
        if (![LoadingState.succeeded, LoadingState.unavailable, LoadingState.saving, ""].includes(statuses[key]))
            return false;
    }
    return true;
}

const handleResult = (res:any, title:string, history:History<LocationState>) => {
    if (res.error) {
        console.error(res.error)
        notification.error({
            message: title,
            description: res.error.message,
            duration: 10
        })
    }
    else {
        if (res.payload?.id)
            history.replace(`/${res.payload.id}`)
        notification.success({
            message: title,
            description: "Success!",
            duration: 10
        })
    }
}

const onSave = (dispatch: AppDispatch, estimate: Estimate|undefined, history:History<LocationState>) => {
    if (estimate) {
        if (!estimate.id)
            dispatch(es.saveEstimate(estimate)).then((res:any)=> handleResult(res, "Create Estimate", history))
        else
            dispatch(es.saveEstimate(estimate)).then((res:any)=> handleResult(res, "Save Estimate", history))
    }
}

const onDelete = (event:any, dispatch: AppDispatch, estimate: Estimate, item:EstimateItem) => {
    dispatch(es.deleteItem(item))
    event && event.stopPropagation()
}

const onCopy = (event:any, dispatch: AppDispatch, estimate: Estimate, item:EstimateItem) => {
    dispatch(es.copyItem(item))
    event && event.stopPropagation()
}

type RenderProductProps = {
    item: EstimateItem
    readOnly: boolean
}

interface ParamTypes {
    eid: string
}

let showingError: string = "";

interface MainProps {
    viewMenu: (eid:number) => JSX.Element
    readOnly: boolean
} 

const Main = ({viewMenu, readOnly}:MainProps) => {
    // Data Access
    const dispatch = useAppDispatch();
    const customerNames: cs.CustomerName[] | undefined = useAppSelector(cs.selectCustomerNames)
    const customerDetailsMap = useAppSelector(cs.selectCustomerDetails)
    const defaults: ds.DefaultData | undefined = useAppSelector(ds.selectDefaults)
    const id2RoomType:ds.RoomTypeConverterFunction = useAppSelector(ds.selectId2RoomTypeConvertor)
    const roomType2Id:ds.RoomTypeConverterFunction = useAppSelector(ds.selectRoomType2IdConvertor)
    const estimate: Estimate | undefined = useAppSelector(es.selectEstimate)
    var params = useParams<ParamTypes>();
    const [showProductOptions, setShowProductOptions] = useState<boolean>(false)
    const history = useHistory();
    const [problems, setWarnings] = useState<EstimateProblem[]>([]);
    const [expandedItems, setExpandedItems] = useState<string | string[]>([]);

    const status_customer = useAppSelector(cs.selectCustomerNamesStatus)
    const status_customerDetail = useAppSelector(cs.selectCustomerDetailsStatus)
    const status_estimate = useAppSelector(es.selectEstimateStatus)
    const status_estimateError = useAppSelector(es.selectEstimateError)
    const status_default = useAppSelector(ds.selectDefaultsStatus)

    // Status collection
    const statuses = useMemo(() => ({
       customer: status_customer,
       customerDetail: status_customerDetail,
       estimate: status_estimate,
       estimateError: status_estimateError,
       default: status_default
    }), [status_customer, status_customerDetail, status_default, status_estimate, status_estimateError])

    if (isFailed(statuses) && showingError === "") {
        showingError = statuses.estimateError
        message.error(statuses.estimateError, undefined, () => {
            showingError = ""
        });
    }

    const onDuplicate = (dispatch: AppDispatch, estimate: Estimate|undefined, history:History<LocationState>) => {
        if (estimate && estimate.id) {
            dispatch(es.duplicateEstimate({ ...estimate, vendor: defaults?.user?.name, vendor_phone: defaults?.user?.phone }))
            history.replace(`/`)
            notification.success({
                message: "Estimate cloned",
                description: "Success!",
                duration: 10
            })
        }
    }
    
    // Customer details
    const [customerId, setCustomerId] = useState<number | undefined>(undefined)
    let customer: cs.CustomerDetails | undefined = undefined;
    if (customerDetailsMap && customerId && customerDetailsMap[customerId]) {
        customer = customerDetailsMap[customerId]
    }

    useEffect(() => {
        if (customer && estimate !== undefined && (estimate?.customer === undefined || estimate?.customer?.id !== customer?.id))
            dispatch(es.updateEstimate({ ...estimate, customer: customer, customerid: Number(customer?.id) }))
    }, [customer, estimate, dispatch])

    // Data retrieval
    useEffect(() => {
        if (estimate === undefined && defaults?.user.name) {
            // If we know the id then load the estimate
            if (params.eid)
                dispatch(es.fetchEstimate(Number(params.eid)))
            else
                dispatch(es.createEstimate(createDefaultEstimate(defaults.user.name, defaults.user.phone, 0)))
        }
    }, [dispatch, params.eid, estimate, defaults?.user.name, defaults?.user.phone])

    useEffect(() => {
        if (customerNames === undefined || customerNames.length === 0)
            dispatch(cs.fetchCustomerNames())
    }, [dispatch, customerNames])

    useEffect(() => {
        if (defaults?.colors.length === 0)
            dispatch(ds.fetchDefaults())
    }, [dispatch, defaults])

    useEffect(() => {
        if (customerId)
            dispatch(cs.fetchCustomerDetailsById(customerId))
    }, [dispatch, customerId])

    // Validation
    useEffect(() => {
        if (estimate)
            setWarnings(validateEstimate(estimate, {id2RoomType, roomType2Id}))
    }, [dispatch, estimate, id2RoomType, roomType2Id])

    useModifiedAlert(estimate)

    const handleAddProduct = (event:any, index:number|undefined) => {
        event && event.stopPropagation()
        setShowProductOptions(true);
    }

    const addImpactProduct = () => {
        setShowProductOptions(false)
        if (estimate && estimate.id)
            history.push(`/impact/${estimate.id}`)
        else
            history.push(`/impact`)
    }

    const addHardware = () => {
        setShowProductOptions(false)
        if (estimate && estimate.id)
            history.push(`/hardware/${estimate.id}`)
        else
            history.push(`/hardware`)
    }

    const addMaterial = () => {
        setShowProductOptions(false)
        if (estimate && estimate.id)
            history.push(`/material/${estimate.id}`)
        else
            history.push(`/material`)
    }

    const addDesign = () => {
        setShowProductOptions(false)
        if (estimate && estimate.id)
            history.push(`/design/${estimate.id}`)
        else
            history.push(`/design`)
    }

    const handleEdit = (event:any, item:EstimateItem) => {
        event && event.stopPropagation()
        switch(item.modtype) {
            case ModuleType.ImpactProduct:
                history.push(`/impact/${estimate?.id}/${item.id}`)
                break
            case ModuleType.Hardware:
                history.push(`/hardware/${estimate?.id}/${item.id}`)
                break
            case ModuleType.Material:
                history.push(`/material/${estimate?.id}/${item.id}`)
                break
            case ModuleType.Design:
                history.push(`/design/${estimate?.id}/${item.id}`)
                break
        }
    }
    
    const onChangeCustomer = (customeridStr: string) => {
        try {
            let cid = Number(customeridStr)
            setCustomerId(cid)
            if (estimate === undefined)
                dispatch(es.updateEstimate(createDefaultEstimate(defaults?.user?.name||"", defaults?.user?.phone||"", cid)))
            else {
                dispatch(es.updateEstimate({ ...estimate, customerid: cid }))
            }
        }
        catch (err) {
            console.error(err)
        }
    }

    const onChangeInternalNotes = (notes: string) => {
        if (estimate !== undefined)
            dispatch(es.updateEstimate({ ...estimate, notes }))
    }

    const onChangePublicNotes = (notes: string) => {
        if (estimate !== undefined)
            dispatch(es.updateEstimate({ ...estimate, public_notes: notes }))
    }

    let handlers:any = null
    if (estimate)
        handlers = { 
            onAdd: (e:any, index:number) => estimate && handleAddProduct(e, index),
            onEdit: (e:any, item:EstimateItem) => estimate && handleEdit(e, item),
            onDelete: (e:any, item:EstimateItem) => estimate && onDelete(e, dispatch, estimate, item),
            onCopy: (e:any, item:EstimateItem) => estimate && onCopy(e, dispatch, estimate, item),
        }

    const RenderProduct = useCallback(({item, readOnly}:RenderProductProps) => {
        const updateItemNo = (val:number) => 
            dispatch(es.updateItem({ ...item, itemno: val }))
        const updateQuantity = (val:number) => 
            dispatch(es.updateItem({ ...item, quantity: val || 1, finalprice: item.sales_price }))
        if (handlers && estimate) {
            switch(item.modtype) {
                case ModuleType.ImpactProduct:
                    return (<ImpactProductComponent readOnly={readOnly} e={estimate} item={item as ImpactProduct} onUpdateQuantity={updateQuantity} onUpdateItemNo={updateItemNo}/> )
                case ModuleType.Hardware:
                    return (<HardwareComponent readOnly={readOnly} e={estimate} item={item as Hardware} onUpdateQuantity={updateQuantity} onUpdateItemNo={updateItemNo}/>)
                case ModuleType.Material:
                    return (<MaterialComponent readOnly={readOnly} e={estimate} item={item as Material} onUpdateQuantity={
                            val => {
                                val = val || 1
                                let updatedItem = { ...item, quantity: val }
                                updatedItem = { ...updatedItem, finalprice: createMaterialItemTotals(estimate,updatedItem,estimate.customer).finalprice }
                                dispatch(es.updateItem(updatedItem))
                            }
                        } onUpdateItemNo={updateItemNo} /> )
                case ModuleType.Design:
                    return (<DesignComponent readOnly={readOnly} e={estimate} item={item as Design} onUpdateQuantity={
                            val => {
                                val = val || 1
                                let updatedItem = { ...item, quantity: val }
                                updatedItem = { ...updatedItem, finalprice: createDesignItemTotals(estimate,updatedItem,estimate.customer).finalprice }
                                dispatch(es.updateItem(updatedItem))
                            }
                        } onUpdateItemNo={updateItemNo} /> )
            }
        }
        return <span>Unknown Product: {item.modtype}</span>
    }, [dispatch, estimate, handlers])

    const shouldSave = ():boolean => {
        return !!(estimate && estimate.customerid && (
            isModified(estimate._modified) || 
            (estimate.items !== undefined && estimate.items.some(i => isModified(i._modified)))) )
    }

    const getHeaderFromItem = useCallback((item:EstimateItem) => {
        let txt = `Opening ${item.itemno}: ${item.name}`
        if (item.modtype === ModuleType.ImpactProduct) {
            let ip:ImpactProduct = item as ImpactProduct
            let roomtype = id2RoomType(ip.roomtype?.toString())
            if (!roomtype || roomtype === "Unknown")
                roomtype = "Room"
            txt += ` - ${roomtype} ${ip.roomnum ?? " number missing"}`
        }

        return (
            <div className="item-summary-header">
                <div className="product-title">{txt}</div>
                <div className="product-price">{asMoney((item.sales_price||0)*(item.quantity||1))}</div>
            </div>
        )
    }, [id2RoomType])

    readOnly = readOnly || isReadOnly(estimate)
    const statusReadOnly = readOnly && !isModified(estimate?._modified || ModifiedState.modified)
    const remainingItems = estimate?.items?.filter(i => !isDeleted(i._modified))
    let itemsList = useMemo(() => {
        let itemsList:React.ReactNode|null = null
        if (isLoading(statuses))
            itemsList = <div className='flex_container_center show4'><Spin style={{ marginLeft: "1rem", display: "flex", alignSelf: "center" }} size="large" /></div>
        if (isFailed(statuses))
            itemsList = <div className='flex_container_center show4'><WarningOutlined style={{ marginLeft: "1rem", display: "flex", alignSelf: "center" }} /></div>
        if (isLoaded(statuses)) {
            if (remainingItems === undefined || remainingItems.length === 0) 
                itemsList = <div className='flex_container_center show4'><Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Empty" style={{margin: "0px"}}/></div>
            else
                itemsList = <Collapse activeKey={expandedItems} onChange={(items) => setExpandedItems(items)} >
                        {
                            remainingItems
                                .sort((a,b) => (a.itemno??0) - (b.itemno??0))
                                .map((item, index) => 
                                <Collapse.Panel className="item-summary" header={getHeaderFromItem(item)} key={index} extra={<ProductButtons readOnly={readOnly} item={item} itemIndex={index+1} {...handlers} />}>
                                    <RenderProduct item={item} readOnly={readOnly} />
                                </Collapse.Panel>)
                        }
                    </Collapse>
        }
        return itemsList
    }, [RenderProduct, expandedItems, getHeaderFromItem, handlers, readOnly, remainingItems, statuses])

    const rejection_reason_missing = "No reason specified"
    const rejection_reason_tip = "Click here to edit reason for rejection"

    const updateEstimateStatus = (s:string) => {
        const status:EstimateStatus = string2status(s)
        if (estimate && estimate.status !== status) {
            const updatedEstimate = {...estimate,status}
            const date = formatDate(new Date())
            switch(status) {
                case EstimateStatus.Approved: {
                    updatedEstimate.approved_date = date
                    break;
                }
                case EstimateStatus.Rejected: {
                    updatedEstimate.rejected_date = date
                    break;
                }
                case EstimateStatus.Delivered: {
                    updatedEstimate.delivered_date = date
                    break;
                }
            }
            dispatch(es.updateEstimate(updatedEstimate))
        }
    }
    const estimateStatusDate = (() => {
        switch(estimate?.status) {
            case EstimateStatus.Approved: return estimate?.approved_date
            case EstimateStatus.Rejected: return estimate?.rejected_date
            case EstimateStatus.Delivered: return estimate?.delivered_date
            default: return undefined
        }
    })()

    const statusMenu = (
        <Menu>
            {Object.keys(EstimateStatus).map((s,i) => <MenuItem key={i} onClick={() => updateEstimateStatus(s) }>{s}</MenuItem>)}
        </Menu>)

    const totals = createEstimateTotals(estimate, estimate?.add_sales_discount||0, estimate?.add_inst_discount||0, estimate?.permits||0, estimate?.salestax||0)
    if (estimate && totals.finalPrice !== estimate.totalprice)
        setTimeout(() => dispatch(es.updateEstimate({...estimate, totalprice: totals.finalPrice})), 0)

    const appRoutes = useAppRoutes()
    const customerData = appRoutes.canPrint() 
        ? <CompactCustomerData 
            estimate={estimate}
            isFailed={isFailed(statuses)}
        />
        : <Initial 
            estimate={estimate}
            customerNames={customerNames}
            onChangeCustomer={onChangeCustomer}
            onUpdateEstimate={(e:Estimate) => dispatch(es.updateEstimate(e))}
            isLoading={isLoading(statuses)}
            isFailed={isFailed(statuses)}
            defaults={defaults}
            readOnly={readOnly}
        />

    const totalsTable = appRoutes.canPrint() 
        ? <TwoColumnTotalsTable 
            totals={totals} 
            includeInstallation={estimate?.is_installation_included || false}
        />
        : <TotalsTable 
            totals={totals} 
            onChangeAdditionalDiscountProductsPercentage={(val:number) => estimate && dispatch(es.updateEstimate({...estimate, add_sales_discount: val}))}
            onChangeAdditionalDiscountInstallPercentage={(val:number) => estimate && dispatch(es.updateEstimate({...estimate, add_inst_discount: val}))}
            onChangePermits={(val:number) => estimate && dispatch(es.updateEstimate({...estimate, permits: val}))}
            onChangeSalesTax={(val:number) => estimate && dispatch(es.updateEstimate({...estimate, salestax: val}))}
            includeInstallation={estimate?.is_installation_included || false}
            readOnly={readOnly}
        />

    return (
        <>
            <Row>
                <Col xs={24}>
                    <Title level={3} style={{ color: '#32445e', textAlign:"center", float: "left", width: "100%" }}>Estimate</Title>
                    {estimate && estimate.id && viewMenu(estimate.id)}
                </Col>
                <Col xs={24}>

                    <Text className="hide-on-print">State:</Text>
                    <Dropdown overlay={statusMenu} disabled={statusReadOnly} className="hide-on-print">
                        <Tag style={{ margin: "1em 0em 2.3em 2em" }} color={status2color(string2status(estimate?.status))}>{estimate?.status} <DownOutlined /></Tag>
                    </Dropdown>

                    {estimateStatusDate && (
                        <Text style={{margin: "1em .5em 2em 1em"}} className="hide-on-print">
                                    Date: {estimateStatusDate}
                        </Text>)}

                    {isRejected(estimate?.status) ? <>
                        <Text style={{margin: "1em .5em 2em 1em"}} className="hide-on-print">
                                Reason:
                        </Text>
                        <Text style={{margin: "1em 2em 2em 0em", width: "20em"}} className="hide-on-print" editable={!statusReadOnly && { 
                                triggerType:["text"], 
                                onChange: val => {
                                    let newReason:string|undefined = val;
                                    if (newReason === rejection_reason_tip)
                                        newReason = undefined
                                    estimate && dispatch(es.updateEstimate({...estimate, status_reason: newReason}))
                                }
                            }}>
                                {estimate?.status_reason || (statusReadOnly ? rejection_reason_missing : rejection_reason_tip )}
                        </Text>
                    </> : ""}
                </Col>
            </Row>
           
            {customerData}

            <Row id="add-save-buttons">
                <Col xs={24} sm={24} md={24} lg={24} xl={24}>
                    <div className='flex_container_end show4'>
                        <Button type="primary" shape="round" icon={<PlusOutlined />} size='large' onClick={() => handleAddProduct(null, 0)} disabled={isNotInProgress(estimate?.status) || !estimate?.customerid || !estimate.id}>Add Product</Button>
                        <Button type="primary" shape="round" icon={statuses.estimate === LoadingState.saving ? <SyncOutlined spin /> : <SaveOutlined /> } size='large' style={{ marginLeft: "2rem" }} onClick={() => onSave(dispatch, estimate, history)} disabled={!shouldSave()}>Save</Button>
                        <Button type="primary" shape="round" icon={<CopyOutlined />} size='large' style={{ marginLeft: "2rem" }} onClick={() => onDuplicate(dispatch, estimate, history)} disabled={estimate&&isDirty(estimate)}>Clone</Button>
                        {!estimate?.permitId?<Button type="primary" shape="round" icon={<AuditOutlined />} size='large' style={{ marginLeft: "2rem" }} onClick={() => window.open("../../amhppermits/buildingpermit_card.php?eid="+estimate?.id+"&action=createFromEID&mainmenu=amhppermits", "_self")} disabled={!estimate||!estimate.id}>Create Permit</Button>:""}
                        {estimate?.permitId?<Button type="primary" shape="round" icon={<AuditOutlined />} size='large' style={{ marginLeft: "2rem" }} onClick={() => window.open("../../amhppermits/buildingpermit_card.php?id="+estimate?.permitId+"&mainmenu=amhppermits", "_self")} disabled={!estimate||!estimate.id}>Show Permit</Button>:""}
                     </div>
                </Col>
            </Row>

            <Row>
                <Col xs={24} sm={24} md={24} lg={24} xl={24}>
                    <Divider />
                    
                    <div className="expand-collapse-buttons"><Button type="link" onClick={() => setExpandedItems(remainingItems?remainingItems?.map((item, index) => index.toString()):[])}>Expand all</Button> | <Button type="link" onClick={() => setExpandedItems([])}>Collapse all</Button></div>
                    {itemsList}

                    <Divider />

                    {problems && problems.length && problems.length>0 ? problems.map((w,i)=>
                        <Alert
                            message={w.type.toUpperCase()}
                            description={w.msg}
                            type={w.type}
                            action={w.link && 
                                <Space>
                                    <Button size="small" type="ghost">
                                    Open Item
                                    </Button>
                                </Space>
                                }
                            showIcon
                            className="hide-on-print"
                            style={{"marginBottom": "2rem"}}
                            key={i}
                        />) : ""
                    }

                    {estimate?.notes ? 
                        <>
                            <Title className="hide-on-print" level={5}>Internal Notes:</Title>
                            <TextArea 
                                placeholder="Notes" 
                                className="hide-on-print"
                                style={{"marginBottom": "2rem"}}
                                autoSize 
                                maxLength={1024}
                                onChange={e => onChangeInternalNotes(e.target.value)}
                                value={estimate?.notes}
                                readOnly={readOnly}
                            />
                        </> : ""}

                    {estimate?.public_notes ? 
                        <>
                            <Title level={5}>Additional Notes:</Title>
                            <TextArea 
                                placeholder="Notes" 
                                style={{"marginBottom": "2rem"}}
                                autoSize 
                                maxLength={1024}
                                onChange={e => onChangePublicNotes(e.target.value)}
                                value={estimate?.public_notes}
                                readOnly={readOnly}
                            />
                        </> : ""}
                </Col>
            </Row>

            {totalsTable}

            <Divider />

            <Row>
                <Col xs={24} sm={24} md={24} lg={24} xl={24}>
                    <EstimateOptions readOnly={readOnly} />
                </Col>
            </Row>

            <ProductOptions 
                visible={showProductOptions} 
                handleCancel={() => setShowProductOptions(false)} 
                handleImpactProduct={addImpactProduct} 
                handleHardware={addHardware} 
                handleMaterial={addMaterial} 
                handleDesign={addDesign} 
            />

            <Space direction="vertical" align="center" style={{width: "100%", marginTop: "1rem"}} className="show-on-print">
                <Alert
                    description={
                        <>
                            <Title level={5} style={{color: "red"}}>
                                There will be a 3% charge for using a credit card<br/>
                                This price is valid for 30 days
                            </Title>
                        </>
                    }
                    type="info"
                    style={{textAlign: "center", backgroundColor: "white", border: "0"}}
                />
            </Space>
        </>
    );
};

export default Main