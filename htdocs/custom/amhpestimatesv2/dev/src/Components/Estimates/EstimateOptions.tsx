import { UpCircleTwoTone } from "@ant-design/icons";
import { Checkbox, Select, Space } from "antd";
import Title from "antd/lib/typography/Title";
import { useEffect, useState } from "react";
import { Estimate } from "../../store/Estimate";
import * as es from '../../store/estimatesSlice';
import { useAppDispatch, useAppSelector } from '../../store/hooks';

const depositPercentDefault=50
const depositWithInstallationPercentDefault=40
const finalInspectionDefault=10
const warrantyYearsDefault=5

export interface EstimateOptionsProps {
    readOnly: boolean
}

const EstimateOptions = ({readOnly}:EstimateOptionsProps) => {
    const dispatch = useAppDispatch();
    const estimate: Estimate | undefined = useAppSelector(es.selectEstimate)

    const deposit_percent = estimate && estimate.deposit_percent !== undefined ? estimate.deposit_percent : 0
    const deposit_percent_with_install = estimate && estimate.deposit_percent_with_install !== undefined ? estimate.deposit_percent_with_install : 0
    const percent_final_inspection = estimate && estimate.percent_final_inspection !== undefined ? estimate.percent_final_inspection : 0

    const warranty_years = estimate && estimate.warranty_years !== undefined ? estimate.warranty_years : 0
    const [warrantyYearsDropdown, setWarrantyYearsDropdown] = useState(warrantyYearsDefault)
    
    const new_construction_owner_responsability = estimate && estimate.new_construction_owner_responsability !== undefined ? estimate.new_construction_owner_responsability : false
    const pay_upon_completion = estimate && estimate.pay_upon_completion !== undefined ? estimate.pay_upon_completion : false

    const noPrintIfZero = (v:number) => v === 0 ? "hide-on-print" : ""
    const noPrintIfFalsy = (v:boolean|number) => v === false || v === 0 ? "hide-on-print" : ""

    useEffect(() => {
        if (estimate) {
            if (!estimate.warranty_years && warrantyYearsDropdown !== warrantyYearsDefault)
                setWarrantyYearsDropdown(warrantyYearsDefault)
            if (estimate.warranty_years && warrantyYearsDropdown !== estimate.warranty_years)
                setWarrantyYearsDropdown(Number(estimate.warranty_years||0))
        }
        else
            setWarrantyYearsDropdown(warrantyYearsDefault)
    }, [estimate, warrantyYearsDropdown])

    const updateWarrantyYears = (val:number) => {
        if (estimate)
            dispatch(es.updateEstimate({...estimate, warranty_years: val}))
    }

    const toggleWarrantyYears = (val:number) => {
        if (estimate) {
            const years = estimate.warranty_years && estimate.warranty_years>0?0:val
            dispatch(es.updateEstimate({...estimate, warranty_years: years}))
        }
    }

    const hasConditions = 
        deposit_percent>0 || 
        deposit_percent_with_install>0 ||
        percent_final_inspection>0 ||
        warranty_years>0 ||
        new_construction_owner_responsability ||
        pay_upon_completion

    const m = { style: {marginTop: ".2em"} }
    const Separator = () => <UpCircleTwoTone style={{margin:"0 1em 0 1em"}} twoToneColor="#aaaaaa"/>

    return (
        <Space size={[0, 0]} direction="vertical" align="center" style={{width: "100%"}} className={"estimate-options " + noPrintIfFalsy(hasConditions)}>
            <Title level={4}>Conditions</Title>
            <div style={{textAlign: "center"}}>
                <Separator/>
                <Space {...m} direction="horizontal" style={{whiteSpace: "nowrap"}} className={noPrintIfZero(deposit_percent)} onClick={(e) => !readOnly && estimate && dispatch(es.updateEstimate({...estimate, deposit_percent: estimate.deposit_percent && estimate.deposit_percent>0?0:depositPercentDefault}))}>
                    <Title style={{marginBottom: "2px"}} level={5}>{depositPercentDefault}% Deposit</Title>
                    <Checkbox 
                        checked={deposit_percent>0} 
                        onChange={(e) => estimate && dispatch(es.updateEstimate({...estimate, deposit_percent: e.target.checked ? depositPercentDefault : 0}))}
                        disabled={readOnly}
                    />
                    <Separator/>
                </Space>
                
                <Space {...m} direction="horizontal" style={{whiteSpace: "nowrap"}} className={noPrintIfZero(deposit_percent_with_install)} onClick={(e) => !readOnly && estimate && dispatch(es.updateEstimate({...estimate, deposit_percent_with_install: estimate.deposit_percent_with_install && estimate.deposit_percent_with_install>0?0:depositWithInstallationPercentDefault}))}>
                    <Title style={{marginBottom: "2px"}} level={5}>{depositWithInstallationPercentDefault}% Deposit With Installation</Title>
                    <Checkbox 
                        checked={deposit_percent_with_install>0} 
                        onChange={(e) => estimate && dispatch(es.updateEstimate({...estimate, deposit_percent_with_install: e.target.checked ? depositWithInstallationPercentDefault : 0}))}
                        disabled={readOnly}
                    />
                    <Separator/>
                </Space>

                <Space {...m} direction="horizontal" style={{whiteSpace: "nowrap"}} className={noPrintIfZero(percent_final_inspection)} onClick={(e) => !readOnly && estimate && dispatch(es.updateEstimate({...estimate, percent_final_inspection: estimate.percent_final_inspection && estimate.percent_final_inspection>0?0:finalInspectionDefault}))}>
                    <Title style={{marginBottom: "2px"}} level={5}>{finalInspectionDefault}% Final Inspection</Title>
                    <Checkbox 
                        checked={percent_final_inspection>0} 
                        onChange={(e) => estimate && dispatch(es.updateEstimate({...estimate, percent_final_inspection: e.target.checked ? finalInspectionDefault : 0}))}
                        disabled={readOnly}
                    />
                    <Separator/>
                </Space>

                <Space {...m} direction="horizontal" style={{whiteSpace: "nowrap"}} className={noPrintIfZero(warranty_years)} >
                    <Select size="small" value={warrantyYearsDropdown} onChange={(val) => { updateWarrantyYears(Number(val)) }} disabled={readOnly} onClick={e => e.preventDefault()} >
                        <Select.Option value="10">10</Select.Option>
                        <Select.Option value="5">5</Select.Option>
                    </Select>
                    <Title style={{marginBottom: "2px", cursor:"pointer"}} onClick={() => !readOnly && toggleWarrantyYears(warrantyYearsDropdown)} level={5}>Years Warranty</Title>
                    <Checkbox 
                        checked={warranty_years>0} 
                        onChange={(e) => updateWarrantyYears(e.target.checked ? warrantyYearsDropdown : 0)}
                        disabled={readOnly}
                    />
                    <Separator/>
                </Space>

                <Space {...m} direction="horizontal" style={{whiteSpace: "nowrap"}} className={noPrintIfFalsy(new_construction_owner_responsability)} onClick={(e) => !readOnly && estimate && dispatch(es.updateEstimate({...estimate, new_construction_owner_responsability: !estimate.new_construction_owner_responsability}))}>
                    <Title style={{marginBottom: "2px"}} level={5}>New construction the buck is owner responsibility</Title>
                    <Checkbox 
                        checked={estimate?.new_construction_owner_responsability} 
                        onChange={(e) => estimate && dispatch(es.updateEstimate({...estimate, new_construction_owner_responsability: e.target.checked}))}
                        disabled={readOnly}
                    />
                    <Separator/>
                </Space>

                <Space {...m} direction="horizontal" style={{whiteSpace: "nowrap"}} className={noPrintIfFalsy(pay_upon_completion)} onClick={(e) => !readOnly && estimate && dispatch(es.updateEstimate({...estimate, pay_upon_completion: !estimate.pay_upon_completion}))}>
                    <Title level={5}>Pay after job is complete</Title>
                    <Checkbox 
                        checked={estimate?.pay_upon_completion} 
                        onChange={(e) => estimate && dispatch(es.updateEstimate({...estimate, pay_upon_completion: e.target.checked}))}
                        disabled={readOnly}
                    />
                    <Separator/>
                </Space>
            </div>
        </Space>
       )
}

export default EstimateOptions;