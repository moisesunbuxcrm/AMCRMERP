import { Col, Divider, Row } from "antd";
import Text from "antd/lib/typography/Text";
import Title from "antd/lib/typography/Title";
import { EstimateTotals } from "../../store/EstimateTotals";
import { asMoney, percentTimes100, round2Decimals } from "../../types/formats";

export type TotalsTableProps = {
    totals:EstimateTotals
    onChangeAdditionalDiscountProductsPercentage?: (val:number) => void
    onChangeAdditionalDiscountInstallPercentage?: (val:number) => void
    onChangePermits?: (val:number) => void
    onChangeSalesTax?: (val:number) => void
    includeInstallation: boolean
    readOnly: boolean
}

const cs1 =  {xs:2,     sm:8,   md:8,   lg:12,  xl:12}
const cs2 =  {xs:14,    sm:10,  md:10,  lg:8,   xl:8}
const cs3 =  {xs:8,     sm:6,   md:6,   lg:4,   xl:4}
const cs23 = {xs:22,    sm:16,  md:16,  lg:12,  xl:12}
const LeftIndent = (props:any) => <Col {...cs1} className={props.className}></Col>
const TotalText = (props:any) => <Col {...cs2} className={props.className}><Title level={5}>{props.children}</Title></Col>
const TotalAmount = (props:any) => <Col {...cs3} style={{textAlign:"right"}} className={props.className}><Text>{props.children}</Text></Col>

const TotalsTable = ({totals, onChangeAdditionalDiscountProductsPercentage, onChangeAdditionalDiscountInstallPercentage, onChangePermits, onChangeSalesTax, includeInstallation, readOnly}: TotalsTableProps) => {
    const additionalDiscountProductsPercentageStr = percentTimes100(totals.additionalDiscountProductsPercentage).toString()
    const additionalDiscountInstallPercentageStr = percentTimes100(totals.additionalDiscountInstallPercentage).toString()
    const permitsStr = round2Decimals(totals.totalPermits).toString()
    const salesTaxStr = percentTimes100(totals.salesTax).toString()

    const updateAdditionalDiscountProductsPercentage = (val:string) => {
        if (!isNaN(Number(val)) && onChangeAdditionalDiscountProductsPercentage !== undefined) {
            let valF = Number(val)/100
            if (valF !== totals.additionalDiscountProductsPercentage) {
                onChangeAdditionalDiscountProductsPercentage(valF)
            }
        }
    }

    const updateAdditionalDiscountInstallPercentage = (val:string) => {
        if (!isNaN(Number(val)) && onChangeAdditionalDiscountInstallPercentage !== undefined) {
            let valF = Number(val)/100
            if (valF !== totals.additionalDiscountInstallPercentage) {
                onChangeAdditionalDiscountInstallPercentage(valF)
            }
        }
    }

    const updatePermits = (val:string) => {
        let valF = Number(val)
        if (!isNaN(valF) && onChangePermits !== undefined) {
            if (valF !== totals.totalPermits) {
                onChangePermits(valF)
            }
        }
    }

    const updateSalesTax = (val:string) => {
        if (!isNaN(Number(val)) && onChangeSalesTax !== undefined) {
            let valF = Number(val)/100
            if (valF !== totals.salesTax) {
                onChangeSalesTax(valF)
            }
        }
    }

    const discountClass = (v:number) => v === 0 ? "hide-on-print" : ""

    return (
        <Row className="totals-table" gutter={[24, 4]}>
            {totals.countImpactProducts > 0 
                ? <>
                    <LeftIndent></LeftIndent>
                    <TotalText>Total Impact Products</TotalText>
                    <TotalAmount>{asMoney(totals.totalImpactProducts)}</TotalAmount>
                </>
                : null}

            {totals.countStormPanels > 0 
                ? <>
                    <LeftIndent></LeftIndent>
                    <TotalText>Total Storm Panels</TotalText>
                    <TotalAmount>{asMoney(totals.totalStormPanels)}</TotalAmount>
                </>
                : null}

            {totals.countHardware > 0 
                ? <>
                    <LeftIndent></LeftIndent>
                    <TotalText>Total Hardware</TotalText>
                    <TotalAmount>{asMoney(totals.totalHardware)}</TotalAmount>
                    </>
                : null}

            {totals.countMaterial > 0 
                ? <>
                    <LeftIndent></LeftIndent>
                    <TotalText>Total Material</TotalText>
                    <TotalAmount>{asMoney(totals.totalMaterial)}</TotalAmount>
                    </>
                : null}

            {totals.countDesign > 0 
                ? <>
                    <LeftIndent></LeftIndent>
                    <TotalText>Total Design</TotalText>
                    <TotalAmount>{asMoney(totals.totalDesign)}</TotalAmount>
                    </>
                : null}

            {includeInstallation ? 
                <>
                    <LeftIndent></LeftIndent>
                    <TotalText>Total Installation</TotalText>
                    <TotalAmount>{asMoney(totals.totalInstallation)}</TotalAmount>
                </> : ""}

            <LeftIndent></LeftIndent>
            <TotalText>Total Other Fees</TotalText>
            <TotalAmount>{asMoney(totals.totalOtherFees)}</TotalAmount>

            <LeftIndent className={discountClass(totals.discountAllProducts)}></LeftIndent>
            <TotalText className={discountClass(totals.discountAllProducts)}>Product Discounts</TotalText>
            <TotalAmount className={discountClass(totals.discountAllProducts)}>{asMoney(-totals.discountAllProducts)}</TotalAmount>

            {includeInstallation ? 
                <>
                    <LeftIndent className={discountClass(totals.discountInstallation)}></LeftIndent>
                    <TotalText className={discountClass(totals.discountInstallation)}>Installation Discounts</TotalText>
                    <TotalAmount className={discountClass(totals.discountInstallation)}>{asMoney(-totals.discountInstallation)}</TotalAmount>
                </> : ""}

            <LeftIndent className={discountClass(totals.additionalDiscountProducts)}></LeftIndent>
            <TotalText className={discountClass(totals.additionalDiscountProducts)}>Additional Product Dsc % <Text editable={!readOnly && { onChange: updateAdditionalDiscountProductsPercentage }}>{additionalDiscountProductsPercentageStr}</Text></TotalText>
            <TotalAmount className={discountClass(totals.additionalDiscountProducts)}>{asMoney(-totals.additionalDiscountProducts)}</TotalAmount>

            {includeInstallation ? 
                <>
                    <LeftIndent className={discountClass(totals.additionalDiscountInstall)}></LeftIndent>
                    <TotalText className={discountClass(totals.additionalDiscountInstall)}>Additional Installation Dsc % <Text editable={!readOnly && { onChange: updateAdditionalDiscountInstallPercentage }}>{additionalDiscountInstallPercentageStr}</Text></TotalText>
                    <TotalAmount className={discountClass(totals.additionalDiscountInstall)}>{asMoney(-totals.additionalDiscountInstall)}</TotalAmount>
                </> : ""}

            <LeftIndent></LeftIndent>
            <TotalText>Permits $<Text editable={!readOnly && { onChange: updatePermits }}>{permitsStr}</Text></TotalText>
            <TotalAmount>{asMoney(totals.totalPermits)}</TotalAmount>

            <LeftIndent></LeftIndent>
            <Col {...cs23}>
                <Divider style={{margin:"12px 0px"}}/>
            </Col>

            <LeftIndent></LeftIndent>
            <TotalText>Subtotal</TotalText>
            <TotalAmount>{asMoney(totals.totalBeforeTax)}</TotalAmount>

            <LeftIndent></LeftIndent>
            <TotalText>Sales Tax %<Text editable={!readOnly && { onChange: updateSalesTax }}>{salesTaxStr}</Text></TotalText>
            <TotalAmount>{asMoney(totals.salesTaxDollars)}</TotalAmount>

            <LeftIndent></LeftIndent>
            <TotalText>Total Amount</TotalText>
            <TotalAmount>{asMoney(totals.finalPrice)}</TotalAmount>
        </Row>)
}

export default TotalsTable;