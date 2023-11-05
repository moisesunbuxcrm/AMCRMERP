import { WarningOutlined } from "@ant-design/icons"
import { Badge, Checkbox, Col, Divider, Input, Row, Select } from "antd"
import Title from "antd/lib/typography/Title"
import { CustomerName, RelationshipType } from "../../store/customersSlice"
import { LabeledData, DefaultData } from '../../store/defaultsSlice';
import { Estimate } from "../../store/Estimate";
import { COLORS } from "../../types/Colors"
import { formatPhone } from "../../types/formats";

type Props = {
    estimate?:Estimate
    customerNames?: CustomerName[]
    onChangeCustomer:(c:string)=>void
    onUpdateEstimate:(e:Estimate)=>void
    isLoading:boolean
    isFailed:boolean
    defaults?: DefaultData
    readOnly: boolean
}

const Initial = ({
    estimate, 
    customerNames, 
    onChangeCustomer,
    onUpdateEstimate,
    isLoading,
    isFailed,
    defaults,
    readOnly
}:Props) => {

    const reltype2Text = (t:number|undefined) => t ? RelationshipType[t] : "None" 
    const id2LabeledData = (id:string) => COLORS.find(c => c.id === id)
    const selectedDefaultFrameColor = estimate?.defcolor?.toUpperCase();
    const selectedDefaultFrameColorHex = selectedDefaultFrameColor && COLORS.find(key => key.name === selectedDefaultFrameColor)?.hex
    const selectedDefaultGlassColor = estimate?.defglasscolor?.toUpperCase();
    const selectedDefaultGlassColorHex = selectedDefaultGlassColor && COLORS.find(key => key.name === selectedDefaultGlassColor)?.hex
    let defaultFrameColorSelector =
        <>
            <Title level={5}>Frame Color</Title>
            <Select<LabeledData>
                showSearch
                className='input show1'
                optionFilterProp="label"
                onChange={e => {
                        if (estimate) 
                            onUpdateEstimate({...estimate, defcolor: id2LabeledData(e as any)?.name })
                    }
                }
                filterOption={(input, option) => option?.label ? option.label.toString().toLowerCase().indexOf(input.toLowerCase()) >= 0 : true}
                options={defaults?.colors}
                value={estimate?.defcolor?.toUpperCase() as any}
                disabled={readOnly}
            />
        </>
    let defaultGlassColorSelector =
        <>
            <Title level={5}>Glass Color</Title>
            <Select<LabeledData>
                showSearch
                className='input show1'
                optionFilterProp="label"
                onChange={e => {
                        if (estimate) 
                            onUpdateEstimate({...estimate, defglasscolor: id2LabeledData(e as any)?.name })
                    }
                }
                filterOption={(input, option) => option?.label ? option.label.toString().toLowerCase().indexOf(input.toLowerCase()) >= 0 : true}
                options={defaults?.colors}
                value={estimate?.defglasscolor?.toUpperCase() as any}
                disabled={readOnly}
            />
    </>

    if (selectedDefaultFrameColorHex)
        defaultFrameColorSelector = 
            <Badge.Ribbon
                text={selectedDefaultFrameColor}
                color={selectedDefaultFrameColorHex}>
                {defaultFrameColorSelector}
            </Badge.Ribbon>

    if (selectedDefaultGlassColorHex)
        defaultGlassColorSelector = 
            <Badge.Ribbon
                text={selectedDefaultGlassColor}
                color={selectedDefaultGlassColorHex}>
                {defaultGlassColorSelector}
            </Badge.Ribbon>

    return <Row className="customer-info" gutter={[24, 24]}>
        <Col xs={24} sm={24} md={12} lg={12} xl={12}>
            <Title level={5}>Customer Name</Title>
            <Select<CustomerName>
                showSearch
                className='input show1'
                optionFilterProp="label"
                onChange={c => {
                    onChangeCustomer(c.toString()) // Not sure why, but c value is not the expected type (cs.CustomerName) - it's just the customer id
                }}
                filterOption={(input, option) => option?.label ? option.label.toString().toLowerCase().indexOf(input.toLowerCase()) >= 0 : true}
                options={customerNames}
                value={estimate?.customer?.customername as any}
                loading={isLoading}
                disabled={readOnly}
            />

            {isFailed ? <WarningOutlined style={{ marginLeft: "1rem", display: "flex", alignSelf: "center" }} /> : null}

            <Title level={5}>Relationship Type</Title>
            <Input className='input show2' placeholder='Relationship Type' disabled={true} value={reltype2Text(estimate?.customer?.reltype)} />

            <Divider style={{marginTop: "0px" }}/>

            <Title level={5}>Estimate Number</Title>
            <Input className='input show2' placeholder='Estimate Number' disabled={true} value={estimate?.estimatenum} />

            <Title level={5}>Customer Name</Title>
            <Input className='input show4' placeholder='Customer Name' disabled={true} value={estimate?.customer?.customername} />

            <Title level={5}>Customer Address</Title>
            <Input className='input show3' placeholder='Customer Address' disabled={true} value={estimate?.customer?.customeraddress} />

            <Title level={5}>Customer Phone</Title>
            <Input className='input show3' placeholder='Customer Phone' disabled={true} value={formatPhone(estimate?.customer?.customerphone)} />

            <Title level={5}>Customer Mobile</Title>
            <Input className='input show3' placeholder='Customer Mobile' disabled={true} value={formatPhone(estimate?.customer?.customermobile)} />


        </Col>

        <Col xs={24} sm={24} md={12} lg={12} xl={12}>

            <Title level={5}>Contact Name</Title>
            <Input className='input show1' placeholder="Contact Name" disabled={true} value={estimate?.customer?.contactname} />

            <Title level={5}>Contact Address</Title>
            <Input className='input show3' placeholder="Contact Address" disabled={true} value={estimate?.customer?.contactaddress} />

            <Title level={5}>Contact Phone</Title>
            <Input className='input show2' placeholder='Contact Phone' disabled={true} value={formatPhone(estimate?.customer?.contactphone)} />

            <Title level={5}>Contact Mobile</Title>
            <Input className='input show4' placeholder='Contact Mobile' disabled={true} value={formatPhone(estimate?.customer?.contactmobile)} />

            <Title level={5}>E-Mail</Title>
            <Input className='input show2' placeholder='E-Mail' disabled={true} value={estimate?.customer?.customeremail} />

            <Title level={5}>Salesperson</Title>
            <Input className='input show4' placeholder='Salesperson' disabled={true} value={estimate?.vendor + (estimate?.vendor_phone ? " ("+formatPhone(estimate?.vendor_phone) + ")" : "")} />

            <Title level={5}>Folio #</Title>
            <Input className='input show4' placeholder='Folio #' disabled={true} value={estimate?.customer?.folionumber} />
        </Col>

        <Col xxl={24}>
            <div className='flex_container show3'>
                <Checkbox
                    checked={estimate?.is_installation_included}
                    onChange={e => {
                            if (estimate) 
                                onUpdateEstimate({...estimate, is_installation_included: e.target.checked })
                        }
                    }
                    disabled={readOnly}
                >
                    <Title level={5}>Include installation services</Title>
                </Checkbox>
                <Checkbox
                    checked={estimate?.is_alteration}
                    onChange={e => {
                            if (estimate) 
                                onUpdateEstimate({...estimate, is_alteration: e.target.checked })
                        }
                    }
                    disabled={readOnly}
                >
                    <Title level={5}>Alteration</Title>
                </Checkbox>
            </div>
        </Col>

        <Col xs={24} sm={24} md={12} lg={12} xl={12}>
            {defaultFrameColorSelector}
        </Col>
        <Col xs={24} sm={24} md={12} lg={12} xl={12}>
            {defaultGlassColorSelector}
        </Col>
    </Row>
}

export default Initial