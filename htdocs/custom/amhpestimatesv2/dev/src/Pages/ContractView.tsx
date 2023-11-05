import { Layout } from "antd";
import { Content } from "antd/lib/layout/layout";
import Contract from "../Components/Estimates/Contract";
import PrintHeader from "../Components/Estimates/PrintHeader";

interface EstimateProps {
    viewMenu: (eid:number) => JSX.Element
} 

const ContractView = ({viewMenu}:EstimateProps) => {
    return (
        <Layout className={"print-estimate"}>
            <Content className='estimateContainer'>
                <PrintHeader />
                <Contract viewMenu={viewMenu}/>
            </Content>
        </Layout>
    );
};

export default ContractView