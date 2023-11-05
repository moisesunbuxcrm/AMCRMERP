import ItemType from '../types/ItemType';
import ModuleType from '../types/ModuleType';
import WinType from '../types/WinType';
import { CustomerDetails, reltype2Markup } from "./customersSlice";
import { Estimate } from './Estimate';
import { DefaultEstimateItem, EstimateItem } from "./EstimateItem";

export interface HardwareTotals {
    sales_price: number
    finalprice: number
}

export interface Hardware extends EstimateItem {
    provider?:string
    product_ref?:string
    hardwaretype?:string
    configuration?:string
}

export const DefaultHardware:Hardware = {
    ...DefaultEstimateItem,
    itemtype: ItemType.Hardware,
    modtype: ModuleType.Hardware,
    provider:undefined,
    product_ref:undefined,
    hardwaretype:undefined,
    configuration:undefined,
    wintype: WinType.None,
}

export const createItemTotals = (e:Estimate, i:Hardware|undefined, c:CustomerDetails|undefined):HardwareTotals => {
    if (i) {
        const sales_price = Math.round(
            ((i.cost_price||0)
            + (i.cost_price||0) * reltype2Markup(c?.reltype))*100)/100
    
        const finalprice = 
            sales_price
            - (i.cost_price||0) * (i.sales_discount||0)
            + (e.is_installation_included ? (i.inst_price||0) - (i.inst_price||0) * (i.inst_discount||0) : 0)
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
