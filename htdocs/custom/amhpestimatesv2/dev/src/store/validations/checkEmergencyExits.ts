import { ImpactProduct } from "../../store/ImpactProduct"
import ModuleType from "../../types/ModuleType"
import { isDeleted } from "../../types/Status"
import WinType from "../../types/WinType"
import { Estimate } from "../Estimate"
import { ValidationHelpers, EstimateProblem, PendingProblems } from "../validations"

const openingLimits:any = {
    [WinType.CasementWindowSeries]: { minWidth: 0, minHeight: 36.75 },
    [WinType.FixedGlass]: { minWidth: 0, minHeight: 0 },
    [WinType.FrenchDoor]: { minWidth: 0, minHeight: 0 },
    [WinType.HorizontalRollingSeries]: { minWidth: 52.375, minHeight: 0 },
    [WinType.SingleHungSeries]: { minWidth: 0, minHeight: 56.75 },
    [WinType.SlidingDoor]: { minWidth: 0, minHeight: 0 },
}

/**
 * Check all bedrooms to make sure they have at least one escape window. Also, highlight any rooms with no type.
 */
export const checkEmergencyExits = (e:Estimate|undefined, h:ValidationHelpers):EstimateProblem[] => {
    const problems:EstimateProblem[] = []

    // Check if room type data is loaded
    const bedroomTypeId = h.roomType2Id("Bedroom")
    if (!bedroomTypeId)
        return [PendingProblems]

    // Get list of rooms that have no type
    const ip = e?.items?.filter(i => !isDeleted(i._modified) && i.modtype === ModuleType.ImpactProduct).map(i => i as ImpactProduct)
    const roomNumbersMissingTypes = ip?.reduce((accum, current) => {
        if (!current.roomtype)
            if (current.roomnum && !accum.includes(current.roomnum))
                accum.push(current.roomnum)

        return accum;
    }, [] as number[])
    if (roomNumbersMissingTypes && roomNumbersMissingTypes.length > 0)
        problems.push({
            msg: `Some rooms are missing a type - so we can't confirm if escape openings are required. Please check room numbers: ${roomNumbersMissingTypes}`,
            link: "",
            type: "warning"
        })

    // Get list of bedrooms that have no number
    const allBedroomItems = ip?.filter(i => i.roomtype?.toString() === bedroomTypeId)
    const countRoomsMissingNumber = allBedroomItems?.reduce((accum, current) => {
        if (current.roomnum === undefined || current.roomnum <= 0)
            accum++
        return accum;
    }, 0)
    if (countRoomsMissingNumber && countRoomsMissingNumber > 0)
        problems.push({
            msg: `${countRoomsMissingNumber} rooms are missing a room number so we can't check for escape openings.`,
            link: "",
            type: "warning"
        })

    // Get list of bedrooms missing an escape window.
    // Step 1: Get list of bedroom numbers
    // Step 2: Get list of bedrooms with an escape window.
    // Step 3: Compare the two lists.
    const allBedroomItemsWithRoomnum = allBedroomItems?.filter(i => i.roomnum !== undefined && i.roomnum > 0)
    const allBedroomNumbers = allBedroomItemsWithRoomnum?.reduce((accum, current) => {
        if (current.roomnum && !accum.includes(current.roomnum))
            accum.push(current.roomnum)                
        return accum;
    }, [] as number[])
    const bedroomNumbersWithEscape = allBedroomItemsWithRoomnum?.reduce((accum, current) => {
        if (current.wintype && current.roomnum && !accum.includes(current.roomnum)) {
            let ol = openingLimits[current.wintype]
            if (ol 
                && current.width
                && current.width >= ol.minWidth
                && current.height
                && current.height >= ol.minHeight)
                accum.push(current.roomnum)
        }
        return accum;
    }, [] as number[])

    const bedroomNumbersMissingEscape = allBedroomNumbers?.filter(i => !bedroomNumbersWithEscape?.includes(i))
    bedroomNumbersMissingEscape?.forEach(n => problems.push({
        msg: `Room ${n} is missing an escape opening.`,
        link: "",
        type: "error"
    }))

    return problems
}
