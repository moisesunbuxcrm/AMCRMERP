import ModuleType from "../types/ModuleType"
import { Estimate, getItems } from "./Estimate"
import { EstimateItem } from "./EstimateItem"
import { ImpactProduct } from "./ImpactProduct"
import { round2Decimals } from "../types/formats";

export interface EstimateTotals {
    countImpactProducts: number
    totalImpactProducts: number
    discountImpactProducts:number
    instImpactProducts: number
    discountInstImpactProducts: number

    countStormPanels: number
    totalStormPanels: number
    discountStormPanels: number
    instStormPanels: number
    discountInstStormPanels: number

    countHardware: number
    totalHardware: number
    discountHardware: number
    instHardware: number
    discountInstHardware: number
    
    countMaterial: number
    totalMaterial: number
    discountMaterial: number
    
    countDesign: number
    totalDesign: number
    discountDesign: number
    
    discountAllProducts: number
    
    totalInstallation: number
    discountInstallation: number

    totalOtherFees: number
    totalDiscount: number

    additionalDiscountProductsPercentage:number
    additionalDiscountProducts: number
    additionalDiscountInstallPercentage: number
    additionalDiscountInstall: number

    totalPermits: number
    totalBeforeTax: number
    salesTax: number
    salesTaxDollars: number
    finalPrice: number
}

// Calculates total of given property in all items matching the product type
const total = (
    items: EstimateItem[],                          // List of items over which to calculate total
    getVal: (i:EstimateItem) => any,                // Extract a value from an estimate item - must return value or undefined
    pt?:ModuleType                                 // Filter for items of this type, must be a valid type or undefined
):number => 
    items.filter(i => pt===undefined || pt === i.modtype)
    .map(i => getVal(i))
    .filter(v => v !== undefined)
    .reduce((t:number, v:number) => t+v, 0)

export const createEstimateTotals = (e:Estimate|undefined, additionalDiscountProductsPercentage:number, additionalDiscountInstallPercentage: number, totalPermits: number, salesTax: number):EstimateTotals => {
    if (e && e.items) {
        let ii:EstimateItem[] = getItems(e)

        const colonialPrice = (i:EstimateItem) => {
            let cp:number = 0
            if (i.modtype === ModuleType.ImpactProduct) {
                let ip = i as ImpactProduct
                if (ip.is_colonial)
                    cp = ip.colonial_fee || 0
            }
            return cp
        }

        const countImpactProducts = ii.filter(i => i.modtype === ModuleType.ImpactProduct).length
        const totalImpactProducts = total(ii, i => (i.sales_price||0) + colonialPrice(i), ModuleType.ImpactProduct)
        const instImpactProducts = e.is_installation_included ? total(ii, i => i.inst_price, ModuleType.ImpactProduct ) : 0
        const discountImpactProducts = total(ii, i => (i.sales_price||0) * (i.sales_discount||0), ModuleType.ImpactProduct )
        const discountInstImpactProducts = e.is_installation_included ? total(ii, i => (i.inst_price||0) * (i.inst_discount||0), ModuleType.ImpactProduct ) : 0

        const countStormPanels = ii.filter(i => i.modtype === ModuleType.StormPanel).length
        const totalStormPanels = total(ii, i => i.sales_price, ModuleType.StormPanel)
        const discountStormPanels = total(ii, i => (i.sales_price||0) * (i.sales_discount||0), ModuleType.StormPanel)
        const instStormPanels = e.is_installation_included ? total(ii, i => i.inst_price, ModuleType.StormPanel) : 0
        const discountInstStormPanels = e.is_installation_included ? total(ii, i => (i.inst_price||0) * (i.inst_discount||0), ModuleType.StormPanel) : 0

        const countHardware = ii.filter(i => i.modtype === ModuleType.Hardware).length
        const totalHardware = total(ii, i => i.sales_price, ModuleType.Hardware)
        const discountHardware = total(ii, i => (i.sales_price||0) * (i.sales_discount||0), ModuleType.Hardware)
        const instHardware = e.is_installation_included ? total(ii, i => i.inst_price, ModuleType.Hardware) : 0
        const discountInstHardware = e.is_installation_included ? total(ii, i => (i.inst_price||0) * (i.inst_discount||0), ModuleType.Hardware) : 0

        const countMaterial = ii.filter(i => i.modtype === ModuleType.Material).length
        const totalMaterial = total(ii, i => (i.sales_price||0)*(i.quantity||1), ModuleType.Material)
        const discountMaterial = total(ii, i => (i.sales_price||0) * (i.quantity||1) * (i.sales_discount||0), ModuleType.Material)

        const countDesign = ii.filter(i => i.modtype === ModuleType.Design).length
        const totalDesign = total(ii, i => (i.sales_price||0)*(i.quantity||1), ModuleType.Design)
        const discountDesign = total(ii, i => (i.sales_price||0) * (i.quantity||1) * (i.sales_discount||0), ModuleType.Design)

        const totalInstallation = e.is_installation_included ? total(ii, i => i.inst_price) : 0
        const discountInstallation = e.is_installation_included ? total(ii, i => (i.inst_price||0) * (i.inst_discount||0)) : 0

        const totalAllProducts = total(ii, i => i.sales_price)
        const totalOtherFees = total(ii, i => i.otherfees)
        const discountAllProducts = total(ii, i => (i.sales_price||0) * (i.sales_discount||0))
        const totalDiscount = discountAllProducts+discountInstallation
        
        const additionalDiscountProducts = totalAllProducts*additionalDiscountProductsPercentage
        const additionalDiscountInstall = totalInstallation*additionalDiscountInstallPercentage
        const totalBeforeTax = 
              totalAllProducts + totalOtherFees - discountAllProducts
            + totalInstallation - discountInstallation
            - additionalDiscountProducts
            - additionalDiscountInstall
            + totalPermits

        const salesTaxDollars = (totalAllProducts - discountAllProducts - additionalDiscountProducts) * (salesTax||0)

        const finalPrice = round2Decimals(totalBeforeTax + salesTaxDollars)

        return {
            countImpactProducts,
            totalImpactProducts,
            instImpactProducts,
            discountImpactProducts,
            discountInstImpactProducts,

            countStormPanels,
            totalStormPanels,
            discountStormPanels,
            instStormPanels,
            discountInstStormPanels,
            
            countHardware,
            totalHardware,
            discountHardware,
            instHardware,
            discountInstHardware,

            countMaterial,
            totalMaterial,
            discountMaterial,

            countDesign,
            totalDesign,
            discountDesign,

            discountAllProducts,

            totalInstallation,
            discountInstallation,

            totalOtherFees,
            totalDiscount,

            additionalDiscountProductsPercentage,
            additionalDiscountProducts,
            additionalDiscountInstallPercentage,
            additionalDiscountInstall,

            totalPermits,
            totalBeforeTax,
            salesTax,
            salesTaxDollars,
            finalPrice
        }
    }
    
    return {
        countImpactProducts:0,
        totalImpactProducts:0,
        instImpactProducts:0,
        discountImpactProducts:0,
        discountInstImpactProducts:0,

        countStormPanels:0,
        totalStormPanels:0,
        discountStormPanels:0,
        instStormPanels:0,
        discountInstStormPanels:0,
        
        countHardware:0,
        totalHardware:0,
        discountHardware:0,
        instHardware:0,
        discountInstHardware:0,

        countMaterial:0,
        totalMaterial:0,
        discountMaterial:0,

        countDesign:0,
        totalDesign:0,
        discountDesign:0,

        discountAllProducts:0,

        totalInstallation:0,
        discountInstallation:0,

        totalOtherFees:0,
        totalDiscount:0,

        additionalDiscountProductsPercentage:0,
        additionalDiscountProducts:0,
        additionalDiscountInstallPercentage:0,
        additionalDiscountInstall:0,

        totalPermits:0,
        totalBeforeTax:0,
        salesTax:0,
        salesTaxDollars:0,
        finalPrice:0
    }
}

