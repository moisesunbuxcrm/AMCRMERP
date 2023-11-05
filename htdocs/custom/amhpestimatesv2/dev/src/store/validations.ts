import { Estimate } from "./Estimate"
import { checkBathroomOpenings } from "./validations/checkBathroomOpenings"
import { checkEmergencyExits } from "./validations/checkEmergencyExits"
import { checkRequiredHardware } from "./validations/checkRequiredHardware"

export interface EstimateProblem {
    msg: string
    link: string
    type: "warning"|"error"|"info"
}

export interface ValidationHelpers {
    id2RoomType: (id?:string) => string|undefined
    roomType2Id: (id?:string) => string|undefined
}

export const PendingProblems:EstimateProblem = {
    msg: "Validating...",
    link: "",
    type: "info"
}

/**
 * Check estimate for required elemnts like escape rooms and opaque windows in bathrooms, etc.
 */
export const validateEstimate = (e:Estimate|undefined, h:ValidationHelpers):EstimateProblem[] => {
    // Check all bathrooms have an interlayer that is clear
    // If an item is a french door or fiberglass then ask for hardware

    const problems:EstimateProblem[] = [] // {msg:"error",link:"yes",type:"error"}, {msg:"warning",link:"",type:"warning"}, {msg:"info",link:"",type:"info"}

    problems.push(...checkEmergencyExits(e,h))
    problems.push(...checkBathroomOpenings(e,h))
    problems.push(...checkRequiredHardware(e,h))

    return problems
}
