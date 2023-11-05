import ItemType from '../types/ItemType'
import ModuleType from '../types/ModuleType'
import { ModifiedState } from '../types/Status'
import WinType from '../types/WinType'

export interface EstimateItem {
    _modified:ModifiedState
    color?: string
    cost_price?: number
    estimateid: number
    finalprice?: number
    id?: number
    image?: string
    inst_discount?: number
    inst_price?: number
    itemno?: number
    itemtype: ItemType
    modtype: ModuleType
    name?: string
    notes?: string
    otherfees?: number
    quantity?: number
    sales_discount?: number
    sales_price?: number
    wintype: WinType
}

export const  DefaultEstimateItem:EstimateItem = {
    _modified: ModifiedState.new,
    color: "",
    cost_price: 0,
    estimateid: 0,
    finalprice: 0,
    id: 0,
    image: "",
    inst_discount: 0,
    inst_price: 0,
    itemno: 1,
    itemtype: ItemType.None,
    modtype: ModuleType.ImpactProduct,
    name: "",
    notes: "",
    otherfees: 0,
    quantity: 1,
    sales_discount: 0,
    sales_price: 0,
    wintype: WinType.None,
}