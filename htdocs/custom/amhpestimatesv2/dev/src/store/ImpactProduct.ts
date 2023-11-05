import ItemType from "../types/ItemType";
import ModuleType from "../types/ModuleType";
import WinType from "../types/WinType";
import { CustomerDetails, reltype2Markup } from "./customersSlice";
import { Estimate } from './Estimate';
import { DefaultEstimateItem, EstimateItem } from "./EstimateItem";

export interface ImpactProductTotals {
    finalprice: number
    sales_price: number
}

export interface ImpactProduct extends EstimateItem {
    coating?:string
    colonial_across?:number
    colonial_down?:number
    colonial_fee?:number
    configuration?:string
    frame_color?:string
    glass_color?:string
    glass_type?:string
    height?: number
    heighttxt?: string
    interlayer?:string
    is_colonial?:boolean
    is_def_color?:boolean
    is_def_glass_color?:boolean
    is_screen?:boolean
    is_standard?:boolean
    length?: number
    lengthtxt?: string
    product_ref?:string
    provider?:string
    room_description?:string
    roomnum?:number
    roomtype?:number
    floornum?:number
    width?: number
    widthtxt?: string
}

export const DefaultImpactProduct:ImpactProduct = {
    ...DefaultEstimateItem,
    colonial_across:0,
    colonial_down:0,
    colonial_fee:0,
    is_colonial:false,
    is_def_color:true,
    is_def_glass_color:true,
    is_screen:false,
    is_standard:true,
    itemtype: ItemType.Window,
    modtype: ModuleType.ImpactProduct,
    room_description:"",
    roomnum:1,
    floornum:1,
    wintype: WinType.SingleHungSeries,
}

export const createItemTotals = (e:Estimate, i:ImpactProduct|undefined, c:CustomerDetails|undefined):ImpactProductTotals => {
    if (i) {
        const sales_price = Math.round(
            ((i.cost_price||0)
            + (i.cost_price||0) * reltype2Markup(c?.reltype))*100)/100
    
        const finalprice = 
            sales_price
            - (i.cost_price||0) * (i.sales_discount||0)
            + (e.is_installation_included ? (i.inst_price||0) - (i.inst_price||0) * (i.inst_discount||0) : 0)
            + (i.is_colonial ? (i.colonial_fee||0) : 0)
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
