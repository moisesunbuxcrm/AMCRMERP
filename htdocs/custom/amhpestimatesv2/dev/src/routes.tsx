import { Suspense, useEffect, useMemo } from 'react';
import AppLayout from './Layout/Layout';
import { Switch, Route, useHistory, useLocation } from "react-router-dom";
import { AppstoreOutlined, CloseOutlined, DollarCircleOutlined, EditOutlined, MenuFoldOutlined, SolutionOutlined } from '@ant-design/icons';
import { Dropdown, Menu } from 'antd';

import Home from './Pages/Home';
import EstimateView from './Pages/EstimateView';
import ModuleImpactProducts from './Pages/ImpactProduct';
import ModuleMaterials from './Pages/Material';
import ModuleDesign from './Pages/Design';
import ModuleHardware from './Pages/Hardware';
import NoPrint from './Pages/NoPrint';
import InvoiceView from './Pages/InvoiceView';
import ContractView from './Pages/ContractView';

interface AppRoutes {
  canPrint: () => boolean
  showEstimate: (eid:number) => void
  showPrintableEstimate: (eid:number) => void
  showInvoice: (eid:number) => void
  showContract: (eid:number) => void
  showNoPrint: () => void
  closeEstimate: () => void
  getMenuKey: () => string
}

export const useAppRoutes = ():AppRoutes => {
  const history = useHistory()
  const location = useLocation()

  return useMemo(() => ({
      canPrint: () => location.pathname.startsWith("/estimate") || location.pathname.startsWith("/invoice") || location.pathname.startsWith("/contract"),
      showEstimate: (eid:number) => history.push(`/${eid}`),
      showPrintableEstimate: (eid:number) => history.push(`/estimate/${eid}`),
      showInvoice: (eid:number) => history.push(`/invoice/${eid}`),
      showContract: (eid:number) => history.push(`/contract/${eid}`),
      showNoPrint: () => history.push(location.pathname.replace('/','/noprint/')),
      closeEstimate: () => { 
        history.push(`/`); 
        window.location.href=window.location.href + "../list.php" 
      },
      getMenuKey: () => location.pathname.startsWith("/estimate") ? "estimate" : location.pathname.startsWith("/invoice") ? "invoice" : location.pathname.startsWith("/contract") ? "contract" : "home"
    }), [history, location])
}

export interface MenuItemData {
  key: string
  title: string
  icon: JSX.Element
  onclick: () => void
}
export type MenuItemInfo = MenuItemData | string

const getMenuItems = (eid:number, appRoutes:AppRoutes):MenuItemInfo[] => [
    {
      key: "home",
      title: "Home",
      icon: <EditOutlined />,
      onclick: () => appRoutes.showEstimate(eid)
    },
    {
      key: "estimate",
      title: "Print Estimate",
      icon: <AppstoreOutlined />,
      onclick: () => appRoutes.showPrintableEstimate(eid)
    },
    {
      key: "invoice",
      title: "Print Invoice",
      icon: <DollarCircleOutlined />,
      onclick: () => appRoutes.showInvoice(eid)
    },
    {
      key: "contract",
      title: "Print Contract",
      icon: <SolutionOutlined />,
      onclick: () => appRoutes.showContract(eid)
    },
    "div",
    {
      key: "close",
      title: "Close Estimate",
      icon: <CloseOutlined />,
      onclick: () => appRoutes.closeEstimate()
    },
  ]

const Routes = () => {
  const appRoutes = useAppRoutes()
  const menuKey = appRoutes.getMenuKey()

  const beforePrint = () => {
    if (!appRoutes.canPrint())
      appRoutes.showNoPrint()
  }

  useEffect(() => {
      window.addEventListener("beforeprint", beforePrint);
      return () => window.removeEventListener("beforeprint", beforePrint)
  })

  const overlay = (eid:number) => 
    <Menu selectedKeys={[menuKey]}>
      {getMenuItems(eid, appRoutes).map((v, i) => {
          if (v==="div")
            return <Menu.Divider key={""+i} style={{margin: ".5em 0em"}} />
          const mi = v as MenuItemData
          return <Menu.Item key={mi.key} icon={mi.icon} onClick={mi.onclick}>{mi.title}</Menu.Item>
        })
      }
    </Menu>

  const menu = (eid:number) => (<Dropdown.Button overlay={overlay(eid)} icon={<MenuFoldOutlined />} style={{ marginLeft: "-5em", marginTop: ".5em"}} className="hide-on-print-only"/>)

  return (
    <div className="estimatesv2">
      <Suspense fallback={<h1>Loading</h1>}>
        <AppLayout>
          <Switch>
            <Route exact path='/hardware/:eid?/:iid?' component={ModuleHardware} />
            <Route path='/impact/:eid?/:iid?' component={ModuleImpactProducts} />
            <Route path='/material/:eid?/:iid?' component={ModuleMaterials} />
            <Route path='/design/:eid?/:iid?' component={ModuleDesign} />
            <Route path='/estimate/:eid?'><EstimateView viewMenu={menu} /></Route>
            <Route path='/invoice/:eid?'><InvoiceView viewMenu={menu} /></Route>
            <Route path='/Contract/:eid?'><ContractView viewMenu={menu} /></Route>
            <Route path='/noprint/:eid?'><NoPrint getMenuItems={getMenuItems}/></Route>
            <Route path='/:eid?'><Home viewMenu={menu} /></Route>
          </Switch>
        </AppLayout>
      </Suspense>
    </div>
  );
}

export default Routes;