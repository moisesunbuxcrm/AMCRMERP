import ItemType from "../types/ItemType";
import ModuleType from "../types/ModuleType";
import { CustomerDetails, reltype2Markup } from "./customersSlice";
import { Estimate } from './Estimate';
import { DefaultEstimateItem, EstimateItem } from "./EstimateItem";

export interface MaterialTotals {
    sales_price: number // unit cost_price plus markup
    finalprice: number // total sales_prices plus installation minus discounts
}

export interface Material extends EstimateItem {
    height?: number
    heighttxt?: string
    length?: number
    lengthtxt?: string
    product_ref?:string
    provider?:string
    width?: number
    widthtxt?: string
}

export const DefaultMaterial:Material = {
    ...DefaultEstimateItem,
    itemtype: ItemType.Hardware,
    modtype: ModuleType.Material
}

export const createItemTotals = (e:Estimate, i:Material|undefined, c:CustomerDetails|undefined):MaterialTotals => {
    if (i) {
        const sales_price = Math.round(
            ((i.cost_price||0)
              + (i.cost_price||0) * reltype2Markup(c?.reltype))*100)/100
    
        const unit_finalprice = 
            sales_price
            - (i.cost_price||0) * (i.sales_discount||0)
            + (e.is_installation_included ? (i.inst_price||0) - (i.inst_price||0) * (i.inst_discount||0) : 0)
        const finalprice =
            unit_finalprice * (i.quantity||1)

        return {
            sales_price,
            finalprice
        }
    }
    
    return {
        sales_price:0,
        finalprice:0
    }
}
