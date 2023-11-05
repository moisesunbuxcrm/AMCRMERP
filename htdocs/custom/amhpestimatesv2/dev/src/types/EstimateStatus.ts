import { Estimate } from "../store/Estimate"

export enum EstimateStatus {
    InProgress = "In Progress",
    Delivered = "Delivered",
    Approved = "Approved",
    Rejected = "Rejected"
}

const colors = {
    [EstimateStatus.InProgress]: "green",
    [EstimateStatus.Delivered]: "#108ee9",
    [EstimateStatus.Approved]: "#87d068",
    [EstimateStatus.Rejected]: "#f50",
}

export const isNotInProgress = (s:EstimateStatus|undefined) => s !== EstimateStatus.InProgress
export const isReadOnly = (e:Estimate|undefined) => !e || isNotInProgress(e?.status)
export const isRejected = (s:EstimateStatus|undefined) => s === EstimateStatus.Rejected
export const status2color = (s:EstimateStatus|undefined) => {
    if (s && colors[s])
        return colors[s]
    return "#f50"
}
export const string2status = (s:string|undefined):EstimateStatus => {
    switch(s?.toLowerCase()) {
        case "delivered": return EstimateStatus.Delivered
        case "approved": return EstimateStatus.Approved
        case "rejected": return EstimateStatus.Rejected
        default: return EstimateStatus.InProgress
    }
}
