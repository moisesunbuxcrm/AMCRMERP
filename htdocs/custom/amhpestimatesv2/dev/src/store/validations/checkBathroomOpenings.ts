import ModuleType from "../../types/ModuleType"
import { isDeleted } from "../../types/Status"
import { Estimate } from "../Estimate"
import { ImpactProduct } from "../ImpactProduct"
import { ValidationHelpers, EstimateProblem, PendingProblems } from "../validations"

/**
 * Warn about all bathrooms that have an interlayer that is clear
 */
export const checkBathroomOpenings = (e:Estimate|undefined, h:ValidationHelpers):EstimateProblem[] => {
    const problems:EstimateProblem[] = []

    // Check if room type data is loaded
    const bathroomTypeId = h.roomType2Id("Bathroom")
    if (!bathroomTypeId)
        return [PendingProblems]

    // Get bathrooms
    const ip = e?.items?.filter(i => !isDeleted(i._modified) && i.modtype === ModuleType.ImpactProduct).map(i => i as ImpactProduct)
    const allBathroomItems = ip?.filter(i => i.roomtype?.toString() === bathroomTypeId)
    const allBathroomItemsWithClearOpening = allBathroomItems?.filter(i => i.interlayer?.toString() === "CLEAR")
    const roomNumbersWithClearOpening = allBathroomItemsWithClearOpening?.reduce((accum, current) => {
        if (current.roomnum && !accum.includes(current.roomnum))
            accum.push(current.roomnum)

        return accum;
    }, [] as number[])
    if (roomNumbersWithClearOpening && roomNumbersWithClearOpening.length > 0)
        problems.push({
            msg: `Some bathrooms have clear openings. Please confirm with client or check room numbers: ${roomNumbersWithClearOpening}`,
            link: "",
            type: "warning"
        })

    return problems
}