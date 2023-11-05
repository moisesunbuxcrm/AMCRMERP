const currency = new Intl.NumberFormat('us-EN', { style: 'currency', currency: 'USD', minimumFractionDigits: 2, maximumFractionDigits: 2 })

const round2Decimals = (value:number|undefined):number => Math.round((value||0)*100)/100
const percentTimes100 = (value:number|undefined):number => Math.round((value||0)*10_000)/100 // Convert to 0..1 value to percentage and round to 2 digits after decimal point avoiding JS rounding errors
const asMoney = (value:number|undefined):string => currency.format(value||0)
const moneyParser = (value:string|undefined):number => Number((value||"").replace(/[^\d.]/g, ''))
const asPercentage = (value:number|undefined):string => `${percentTimes100(value)}%`
const percentageParser = (value:string|undefined):number => Number((value||"").replace('%', ''))/100

const phoneRegEx = /[^0-9]/g
const formatPhone = (txt:string|undefined):string|undefined => {
    let digits:string = (txt||"").replaceAll(phoneRegEx,"")

    if (digits !== "") {
        // remove extra digits
        digits = digits.substring(0,10)

        // add missing digits
        if (digits.length < 10)
            digits = "0000000000".substring(digits.length) + digits

        return digits.substring(0,3) + "-" + digits.substring(3,6) + "-" + digits.substring(6);
    }
    return undefined
}

export { asMoney, moneyParser, asPercentage, percentageParser, percentTimes100, round2Decimals, formatPhone }