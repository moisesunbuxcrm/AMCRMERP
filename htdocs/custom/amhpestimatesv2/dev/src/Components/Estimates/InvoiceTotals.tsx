import { Col, Row } from "antd";
import Text from "antd/lib/typography/Text";
import Title from "antd/lib/typography/Title";
import { EstimateTotals } from "../../store/EstimateTotals";
import { asMoney } from "../../types/formats";

export type TotalsTableProps = {
    totals:EstimateTotals
}

const cs1 =  {xs:2,     sm:8,   md:8,   lg:12,  xl:12}
const cs2 =  {xs:14,    sm:10,  md:10,  lg:8,   xl:8}
const cs3 =  {xs:8,     sm:6,   md:6,   lg:4,   xl:4}
const LeftIndent = (props:any) => <Col {...cs1} className={props.className}></Col>
const TotalText = (props:any) => <Col {...cs2} className={props.className}><Title level={3}>{props.children}</Title></Col>
const TotalAmount = (props:any) => <Col {...cs3} style={{textAlign:"right"}} className={props.className}><Text>{props.children}</Text></Col>

const InvoiceTotals = ({totals}: TotalsTableProps) => {
    return (
        <Row className="invoice-totals-table" gutter={[24, 4]}>
            <LeftIndent></LeftIndent>
            <TotalText>Total Amount</TotalText>
            <TotalAmount>{asMoney(totals.finalPrice)}</TotalAmount>
        </Row>)
}

export default InvoiceTotals;