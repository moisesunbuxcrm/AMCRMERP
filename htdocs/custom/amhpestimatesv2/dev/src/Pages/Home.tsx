import { Layout } from "antd";
import { Content } from "antd/lib/layout/layout";
import Main from "../Components/Estimates/Main";

interface EditProps {
    viewMenu: (eid:number) => JSX.Element
} 

/** Default view for editing Estimate */
const Home = ({viewMenu}:EditProps) => {
    return (
        <Layout className={"home"}>
            <Content className='estimateContainer'>
                <Main readOnly={false} viewMenu={viewMenu}/>
            </Content>
        </Layout>
    )
}

export default Home