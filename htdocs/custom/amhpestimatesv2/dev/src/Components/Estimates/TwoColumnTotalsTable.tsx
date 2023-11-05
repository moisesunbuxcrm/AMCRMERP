import { Col, Row, Table } from "antd"
import { ColumnsType } from "antd/lib/table";
import { EstimateTotals } from "../../store/EstimateTotals";
import { asMoney, percentTimes100 } from "../../types/formats";

type Props = {
    totals:EstimateTotals
    includeInstallation: boolean
}

interface CompactTotalsTableType {
    label: string
    value: string
    key: string
}

const columns: ColumnsType<CompactTotalsTableType> = [
    {
        title: 'Label',
        dataIndex: 'label',
        key: 'label',
        render: text => <b style={{ whiteSpace: "nowrap", verticalAlign: "top"}}>{text}</b>,
        align: 'right',
        className: 'CCDLabel',
        width: 300
    },
    {
        title: 'Value',
        dataIndex: 'value',
        key: 'value',
        className: 'CCDValue',
        width: 100
    },
];

const TwoColumnTotalsTable = ({
    totals, 
    includeInstallation,
}:Props) => {
    const salesTaxStr = percentTimes100(totals.salesTax).toString()

    const rightData:CompactTotalsTableType[] = []
    const addDataRow = (label:string, value:number|undefined) => {
        if (value)
            rightData.push({label:label+":", value: asMoney(value), key: label})
    }
    const addDataRowAlways = (label:string, value:number|undefined) => {
        rightData.push({label:label+":", value: value?asMoney(value):asMoney(0), key: label})
    }

    addDataRow('Total Impact Products', totals.totalImpactProducts)
    addDataRow('Total Storm Panels', totals.totalStormPanels)
    addDataRow('Total Hardware', totals.totalHardware)
    addDataRow('Total Material', totals.totalMaterial)
    addDataRow('Total Design', totals.totalDesign)
    if (includeInstallation)
        addDataRow('Total Installation', totals.totalInstallation)

    addDataRow('Total Other Fees', totals.totalOtherFees)
    addDataRow('Product Discounts', -totals.discountAllProducts)
    if (includeInstallation)
        addDataRow('Installation Discounts', -totals.discountInstallation)

    addDataRow('Additional Product Dsc', -totals.additionalDiscountProducts)
    addDataRow('Additional Installation Dsc', -totals.additionalDiscountInstall)
    addDataRowAlways('Permits', totals.totalPermits)
    addDataRowAlways('Subtotal', totals.totalBeforeTax)
    addDataRowAlways('Sales Tax %'+salesTaxStr, totals.salesTaxDollars)
    addDataRowAlways('Total Amount', totals.finalPrice)

    const leftData:CompactTotalsTableType[] = rightData.splice(0, rightData.length/2,)

    return <Row className="customer-info" gutter={[24, 24]}>
        <Col md={12}>
            <Table dataSource={leftData} columns={columns} showHeader={false} pagination={false} size="small" className="totals-table" />
        </Col>

        <Col md={12}>
            <Table dataSource={rightData} columns={columns} showHeader={false} pagination={false} size="small" className="totals-table" />
        </Col>
    </Row>
}

export default TwoColumnTotalsTable