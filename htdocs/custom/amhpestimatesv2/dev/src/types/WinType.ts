enum WinType {
    CasementWindowSeries = "Casement Window Series",
    FrenchDoor = "French Door",
    FixedGlass = "Fixed Glass",
    HorizontalRollingSeries = "Horizontal Rolling Series",
    SingleHungSeries = "Single Hung Series",
    SlidingDoor = "Sliding Door",
    None = ""
}

export const WinTypes:string[] = Object.values(WinType).filter(value => isNaN(Number(value)))


export default WinType 