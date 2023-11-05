import { Layout } from "antd";
import { Content } from "antd/lib/layout/layout";
import Main from "../Components/Estimates/Main";
import PrintHeader from "../Components/Estimates/PrintHeader";

interface EstimateProps {
    viewMenu: (eid:number) => JSX.Element
} 

const EstimateView = ({viewMenu}:EstimateProps) => {
    return (
        <Layout className={"print-estimate"}>
            <Content className='estimateContainer'>
                <PrintHeader />
                <Main readOnly={true} viewMenu={viewMenu}/>
            </Content>
        </Layout>
    );
};

export default EstimateView