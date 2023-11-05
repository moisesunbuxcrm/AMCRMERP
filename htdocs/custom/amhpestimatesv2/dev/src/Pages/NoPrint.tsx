import { Button, Divider, Space } from "antd"
import Title from "antd/lib/typography/Title";
import { useHistory, useParams } from "react-router";
import { MenuItemData, MenuItemInfo, useAppRoutes } from "../routes";

interface NoPrintProps {
    getMenuItems: (eid:number, history:any) => MenuItemInfo[]
}

const NoPrint = ({getMenuItems}:NoPrintProps) => {
    const appRoutes = useAppRoutes()
    const history = useHistory()
    const { eid } = useParams<{eid?:string}>()

    return (
        <Space direction="vertical" align="center" style={{width: "100%", marginTop: "4em"}}>
            <Title>Please do not print this page.</Title>
            <Button type="primary" onClick={() => history.goBack()} style={{marginBottom: "4em"}}>Back</Button>
            {getMenuItems(Number(eid), appRoutes).map(mi => {
                    if (mi === "div")
                        return <Divider style={{margin: "1em 0em", width: "10em"}}/>
                    const md = mi as MenuItemData 
                    return <Button type="primary" onClick={md.onclick}>{md.title}</Button>
                })
            }
        </Space>
    )
}

export default NoPrint