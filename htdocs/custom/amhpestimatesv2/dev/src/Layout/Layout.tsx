import React from 'react';
import './Layout.css';
import { Layout } from 'antd';

const { Content } = Layout;

type AppLayoutProps = {
    children: React.ReactNode;
}

const AppLayout = ({ children }: AppLayoutProps) => {
    return (
        <Layout>
            <Content>
                {children}
            </Content>
        </Layout>

    );
};

export default AppLayout;