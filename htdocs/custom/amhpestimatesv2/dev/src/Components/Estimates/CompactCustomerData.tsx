import { Checkbox, Col, Row, Table } from "antd"
import { ColumnsType } from "antd/lib/table";
import Title from "antd/lib/typography/Title"
import { RelationshipType } from "../../store/customersSlice"
import { Estimate } from "../../store/Estimate";
import { formatPhone } from "../../types/formats";

type Props = {
    estimate?:Estimate
    isFailed:boolean
}

interface CompactCustomerDataType1 {
    label1: string
    value1: string
}

interface CompactCustomerDataType2 {
    label2: string
    value2: string
}

interface CompactCustomerDataType extends CompactCustomerDataType1, CompactCustomerDataType2 {
    key: string
}

const columns: ColumnsType<CompactCustomerDataType> = [
    {
        title: 'Label1',
        dataIndex: 'label1',
        key: 'label1',
        render: text => <b style={{ whiteSpace: "nowrap", verticalAlign: "top"}}>{text}</b>,
        align: 'left',
        className: 'CCDLabel'
    },
    {
        title: 'Value1',
        dataIndex: 'value1',
        key: 'value1',
        render: text => <b style={{ verticalAlign: "top"}}>{text}</b>,
        className: 'CCDValue',
        onCell: (_, index) => ({
            colSpan: (index as number) === 0 ? 3 : 1,
          }),
    },
    {
        title: 'Label2',
        dataIndex: 'label2',
        key: 'label2',
        render: text => <b style={{ whiteSpace: "nowrap", verticalAlign: "top"}}>{text}</b>,
        align: 'left',
        className: 'CCDLabel',
        onCell: (_, index) => ({
            colSpan: (index as number) === 0 ? 0 : 1,
          }),
    },
    {
        title: 'Value2',
        dataIndex: 'value2',
        key: 'value2',
        render: text => <b style={{ verticalAlign: "top"}}>{text}</b>,
        className: 'CCDValue',
        onCell: (_, index) => ({
            colSpan: (index as number) === 0 ? 0 : 1,
          }),
    },
];

/**
 * Takes an array of address components (like street, state, zip, commas, and newlines)
 * and creates a string where newlines and commas are removed if not neeed because address
 * components are missing
 */
const address2String = (a:(string|undefined)[]):string => {
    let lines:string[] = []
    let line = ""
    a.forEach((v, i) => {
        v = v || ""
        if (v === "\n") {
            if (line !== "") {
                lines.push(line.trim() + v);
                line = ""
            }
        }
        else if (v === ",") {
            if (line !== "" && !line.endsWith(", "))
                line += ", "
        }
        else if (v !== "") {
            if (line !== "" && !line.endsWith(" "))
                line += " "
            line += v
        }
    })
    if (line !== "")
        lines.push(line.trim());
    return lines.join("")
} 

// const checkAddress = (a:string, b:string) => {
//     console.log("\"" + a + "\" == \"" + b + "\"")
//     console.assert(a === b)
// }

const CompactCustomerData = ({
    estimate, 
    isFailed
}:Props) => {
    // checkAddress(address2String(["1 Main St.", ",", "\n", "New York", ",", "\n", "NY", "10001"]), "1 Main St.,\nNew York,\nNY 10001")
    // checkAddress(address2String(["1 Main St.", ",", "\n", undefined, ",", "\n", "NY", "10001"]), "1 Main St.,\nNY 10001")
    // checkAddress(address2String([undefined, "Main St.", ",", "\n", "New York", ",", "\n", "NY", "10001"]), "Main St.,\nNew York,\nNY 10001")
    // checkAddress(address2String([undefined, ",", "\n", undefined, ",", "\n", undefined, undefined]), "")
    // checkAddress(address2String(["1 Main St.", ",", "\n", "New York", ",", "\n", "NY", undefined]), "1 Main St.,\nNew York,\nNY")
    
    const reltype2Text = (t:number|undefined) => t ? RelationshipType[t] : "None" 

    // To create the table of customer data
    // Collect data for left side in cols1 and the data for the right side in cols2
    // Then merge them all into rows for the table

    const cols1:CompactCustomerDataType1[] = []
    const cols2:CompactCustomerDataType2[] = []
    const createDataRow1 = (label:string, value:string):CompactCustomerDataType1 => ({label1:label?label+":":"", value1:value})
    const createDataRow2 = (label:string, value:string):CompactCustomerDataType2 => ({label2:label?label+":":"", value2:value})
    const addDataRow1 = (label:string, value:string|undefined, always:boolean = false) => {
        if (value || always)
            cols1.push(createDataRow1(label, value||""))
    }
    const addDataRow2 = (label:string, value:string|undefined, always:boolean = false) => {
        if (value || always)
            cols2.push(createDataRow2(label, value||""))
    }
    const addDataRowShortest = (label:string, value:string|undefined, always:boolean = false) => {
        if (value || always) {
            if (cols1.length > cols2.length)
                cols2.push(createDataRow2(label, value||""))
            else
                cols1.push(createDataRow1(label, value||""))
        }
    }

    addDataRow1('Estimate #', estimate?.estimatenum, true)
    addDataRow1('Customer Phone', formatPhone(estimate?.customer?.customerphone))
    addDataRow1('Customer Mobile', formatPhone(estimate?.customer?.customermobile))
    addDataRow1('Customer E-Mail', estimate?.customer?.customeremail)

    // Address needs to use several rows
    let address:string[] = address2String([
        estimate?.customer?.customeraddress, ",", "\n", 
        estimate?.customer?.customercity, "," ,"\n", 
        estimate?.customer?.customerstate, estimate?.customer?.customerzip]).split("\n")
    address.forEach((e,i) => addDataRow2(i === 0?'Customer Address':'', e));
    addDataRow2('Folio #', estimate?.customer?.folionumber)
    addDataRow2('Salesperson', estimate?.vendor + (estimate?.vendor_phone ? " ("+formatPhone(estimate?.vendor_phone) + ")" : ""))

    addDataRowShortest('Contact Name', estimate?.customer?.contactname)
    addDataRowShortest('Contact Address', estimate?.customer?.contactaddress)
    addDataRowShortest('Contact Phone', formatPhone(estimate?.customer?.contactphone))
    addDataRowShortest('Contact Mobile', formatPhone(estimate?.customer?.contactmobile))
    addDataRowShortest('Relationship Type', reltype2Text(estimate?.customer?.reltype))
    addDataRowShortest('Frame Color', estimate?.defcolor?.toUpperCase())
    addDataRowShortest('Glass Color', estimate?.defglasscolor?.toUpperCase())
   
    // Merges cols1 and cols2 into an array of rows with customer name in first row
    let cdrows:CompactCustomerDataType[] = [{key:"cn", label1:"Customer Name:", value1:estimate?.customer?.customername||"", label2:" ", value2:" "}] 
    let i:number=0
    while(cols1.length > i || cols2.length > i) {
        let row:CompactCustomerDataType = {key:""+i, label1:" ", value1:" ", label2:"", value2:""}
        if (cols1.length > i)
            row = {...row, label1:cols1[i].label1, value1:cols1[i].value1 }
        if (cols2.length > i)
            row = {...row, label2:cols2[i].label2, value2:cols2[i].value2 }
        cdrows.push(row)
        i++
    }

    const hide_is_installation_included = !estimate?.is_installation_included
    const hide_is_alteration = !estimate?.is_alteration
    const hide_both = hide_is_installation_included && hide_is_alteration

    return <>
        <Row className="customer-info" gutter={[24, 24]}>
            <Col xl={24}>
                <Table 
                    dataSource={cdrows} 
                    columns={columns} 
                    showHeader={false} 
                    pagination={false} 
                    size="small" 
                    rowClassName={(record, index) => index === 0 ? 'table-row-customername' :  ''}
                />
            </Col>

            <Col className={hide_both ? "hide-on-print" : ""}>
                <div className='flex_container show3' >
                    <Checkbox
                        checked={estimate?.is_installation_included}
                        disabled={true}
                        className={hide_is_installation_included ? "hide-on-print" : ""}
                    >
                        <Title level={5}>Include installation services</Title>
                    </Checkbox>
                    <Checkbox
                        checked={estimate?.is_alteration}
                        disabled={true}
                        className={hide_is_alteration ? "hide-on-print" : ""}
                    >
                        <Title level={5}>Alteration</Title>
                    </Checkbox>
                </div>
            </Col>
        </Row>
    </>
}

export default CompactCustomerData