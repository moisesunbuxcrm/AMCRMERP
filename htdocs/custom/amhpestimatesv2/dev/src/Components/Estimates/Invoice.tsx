import { useEffect, useState } from 'react';
import '../../Styles/main.css';
import { Typography, Divider, message, Collapse, Row, Col, Button, Spin, Empty, Alert, Space } from 'antd';
import { useAppDispatch, useAppSelector } from '../../store/hooks';
import * as cs from '../../store/customersSlice';
import * as es from '../../store/estimatesSlice';
import * as ds from '../../store/defaultsSlice';
import { useParams } from "react-router-dom";
import ModuleType from '../../types/ModuleType';
import ImpactProductComponent from '../ProductSummary/ImpactProductComponent';
import HardwareComponent from '../ProductSummary/HardwareComponent';
import { Hardware } from '../../store/Hardware';
import { ImpactProduct } from '../../store/ImpactProduct';
import { EstimateItem } from '../../store/EstimateItem';
import { ProductButtons } from '../ProductSummary/ProductComponent';
import { isDeleted, isModified, LoadingState } from '../../types/Status';
import { Estimate } from '../../store/Estimate';
import { WarningOutlined } from '@ant-design/icons';
import { asMoney } from "../../types/formats";
import TextArea from 'antd/lib/input/TextArea';
import CompactCustomerData from './CompactCustomerData';
import { createEstimateTotals } from '../../store/EstimateTotals';
import InvoiceTotals from './InvoiceTotals';
import EstimateOptions from './EstimateOptions';
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
} 

const Invoice = ({viewMenu}:MainProps) => {
    // Data Access
    const dispatch = useAppDispatch();
    const defaults: ds.DefaultData | undefined = useAppSelector(ds.selectDefaults)
    const id2RoomType:ds.RoomTypeConverterFunction = useAppSelector(ds.selectId2RoomTypeConvertor)
    const estimate: Estimate | undefined = useAppSelector(es.selectEstimate)
    var params = useParams<ParamTypes>();
    const [expandedItems, setExpandedItems] = useState<string | string[]>([]);

    // Status
    const statuses = {
        customer: useAppSelector(cs.selectCustomerNamesStatus),
        customerDetail: useAppSelector(cs.selectCustomerDetailsStatus),
        estimate: useAppSelector(es.selectEstimateStatus),
        estimateError: useAppSelector(es.selectEstimateError),
        default: useAppSelector(ds.selectDefaultsStatus)
    }

    if (isFailed(statuses) && showingError === "") {
        showingError = statuses.estimateError
        message.error(statuses.estimateError, undefined, () => {
            showingError = ""
        });
    }

    // Data retrieval
    useEffect(() => {
        if (estimate === undefined && defaults?.user.name) {
            dispatch(es.fetchEstimate(Number(params.eid)))
        }
    }, [dispatch, params.eid, estimate, defaults?.user.name])

    useEffect(() => {
        if (defaults?.colors.length === 0)
            dispatch(ds.fetchDefaults())
    }, [dispatch, defaults])

    const RenderProduct = ({item, readOnly}:RenderProductProps) => {
        const updateItemNo = (val:number) => 
            dispatch(es.updateItem({ ...item, itemno: val }))
        const updateQuantity = (val:number) => 
            dispatch(es.updateItem({ ...item, quantity: val }))
        if (estimate) {
            switch(item.modtype) {
                case ModuleType.ImpactProduct:
                    return (<ImpactProductComponent readOnly={readOnly} e={estimate} item={item as ImpactProduct} onUpdateQuantity={updateQuantity} onUpdateItemNo={updateItemNo}/> )
                case ModuleType.Hardware:
                    return (<HardwareComponent readOnly={readOnly} e={estimate} item={item as Hardware} onUpdateQuantity={updateQuantity} onUpdateItemNo={updateItemNo}/>)
                case ModuleType.Material:
                    return (<MaterialComponent readOnly={readOnly} e={estimate} item={item as Hardware} onUpdateQuantity={updateQuantity} onUpdateItemNo={updateItemNo}/>)
                case ModuleType.Design:
                    return (<DesignComponent readOnly={readOnly} e={estimate} item={item as Hardware} onUpdateQuantity={updateQuantity} onUpdateItemNo={updateItemNo}/>)
            }
        }
        return <span>Unknown Product: {item.modtype}</span>
    }

    const getHeaderFromItem = (item:EstimateItem) => {
        let txt = `Opening ${item.itemno}: ${item.name}`
        if (item.modtype === ModuleType.ImpactProduct) {
            let ip:ImpactProduct = item as ImpactProduct
            let roomtype = id2RoomType(ip.roomtype?.toString())
            if (!roomtype || roomtype === "Unknown")
                roomtype = "Room"
            txt += ` - ${roomtype} ${ip.roomnum ?? " number missing"}`
        }
        txt += ` - ${asMoney(item.sales_price)}`
        if (isModified(item._modified))
            return <Text italic strong>{txt}</Text>
        return <Text>{txt}</Text>
    }

    const readOnly = true
    const remainingItems = estimate?.items?.filter(i => !isDeleted(i._modified))
    let itemsList = null
    if (isLoading(statuses))
        itemsList = <div className='flex_container_center show4'><Spin style={{ marginLeft: "1rem", display: "flex", alignSelf: "center" }} size="large" /></div>
    if (isFailed(statuses))
        itemsList = <div className='flex_container_center show4'><WarningOutlined style={{ marginLeft: "1rem", display: "flex", alignSelf: "center" }} /></div>
    if (isLoaded(statuses)) {
        if (remainingItems === undefined || remainingItems.length === 0) 
            itemsList = <div className='flex_container_center show4'><Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Empty" style={{margin: "0px"}}/></div>
        else
            itemsList = <Collapse activeKey={expandedItems} onChange={(items) => setExpandedItems(items)}>
                    {
                        remainingItems
                            .sort((a,b) => (a.itemno??0) - (b.itemno??0))
                            .map((item, index) => 
                            <Collapse.Panel className="item-summary" header={getHeaderFromItem(item)} key={index} extra={<ProductButtons readOnly={readOnly} item={item} itemIndex={index+1} />}>
                                <RenderProduct item={item} readOnly={readOnly} />
                            </Collapse.Panel>)
                    }
                </Collapse>
    }

    const totals = createEstimateTotals(estimate, estimate?.add_sales_discount||0, estimate?.add_inst_discount||0, estimate?.permits||0, estimate?.salestax||0)
    
    const customerData = <CompactCustomerData 
            estimate={estimate}
            isFailed={isFailed(statuses)}
        />

    return (
        <>
            <Row>
                <Col xs={24}>
                    <Title level={3} style={{ color: '#32445e', textAlign:"center", float: "left", width: "100%" }}>Invoice</Title>
                    {estimate && estimate.id && viewMenu(estimate.id)}
                </Col>
            </Row>
           
            {customerData}

            <Row>
                <Col xs={24} sm={24} md={24} lg={24} xl={24}>
                    <Divider />
                    
                    <div className="expand-collapse-buttons"><Button type="link" onClick={() => setExpandedItems(remainingItems?remainingItems?.map((item, index) => index.toString()):[])}>Expand all</Button> | <Button type="link" onClick={() => setExpandedItems([])}>Collapse all</Button></div>
                    {itemsList}

                    <Divider />

                    <Title className="hide-on-print" level={5}>Internal Notes:</Title>
                    <TextArea 
                        showCount 
                        placeholder="Internal Notes" 
                        className="hide-on-print"
                        style={{"marginBottom": "2rem"}}
                        autoSize 
                        maxLength={1024}
                        value={estimate?.notes}
                        readOnly={readOnly}
                    />

                    <Title level={5}>Additional Notes:</Title>
                    <TextArea 
                        showCount 
                        placeholder="Notes" 
                        style={{"marginBottom": "2rem"}}
                        autoSize 
                        maxLength={1024}
                        value={estimate?.public_notes}
                        readOnly={readOnly}
                    />
                </Col>
            </Row>

            <InvoiceTotals totals={totals} />

            <Divider />

            <Row>
                <Col xs={24} sm={24} md={24} lg={24} xl={24}>
                    <EstimateOptions readOnly={readOnly} />
                </Col>
            </Row>

            <Space direction="vertical" align="center" style={{width: "100%", marginTop: "1rem"}} className="show-on-print">
                <Alert
                    description={
                        <>
                            <Title level={5} style={{color: "red"}}>
                                There will be a 3% charge for using a credit card
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

export default Invoice