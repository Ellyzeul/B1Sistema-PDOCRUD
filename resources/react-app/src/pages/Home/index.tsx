import { useContext } from "react"
import { BookQuotesCard } from "../../components/BookQuotesCard"
import { UserData } from "../../components/LoginForm/types"
import { Navbar } from "../../components/Navbar"
import { SectionButton } from "../../components/SectionButton"
import { UserInfoCard } from "../../components/UserInfoCard"
import { UserDataContext } from "../../contexts/UserData"
import "./style.css"

export const HomePage = () => {
  const userDataContext = useContext(UserDataContext)
  const userData = userDataContext[0] as UserData

  return (
    <div className={"home-page"}>
      <Navbar items={[]} />
      <div className={"home-page-content"}>
        <div className={"home-page-user-info"}>
          <div>
            <BookQuotesCard userName={userData.name} />
          </div>
          <div>
            <UserInfoCard userData={userData} />
          </div>
        </div>
        <div className={"home-page-sections"}>
          <SectionButton icon={"/icons/gestao.png"} label={"Controle de pedidos"} url={"/pedidos"} />
          <SectionButton icon={"/icons/fotos.png"} label={"Fotos dos pedidos"} url={"/fotos/enviar"} />
        </div>
        <div className={"home-page-notifications"}>
          Sample Text
        </div>
      </div>
    </div>
  )
}
