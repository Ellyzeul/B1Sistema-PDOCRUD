import { useContext, useEffect, useState } from "react"
import { BookQuotesCard } from "../BookQuotesCard"
import { UserData } from "../LoginForm/types"
import { Navbar } from "../Navbar"
import { SectionButton } from "../SectionButton"
import { UserInfoCard } from "../UserInfoCard"
import { UserDataContext } from "../../contexts/UserData"
import "./style.css"
import { HomeProp } from "./types"

export const Home = (props: HomeProp) => {
  const { sections } = props
  const userDataContext = useContext(UserDataContext)
  const userData = userDataContext[0] as UserData
  const [sectionsList, setSectionsList] = useState([] as JSX.Element[])

  useEffect(() => {
    setSectionsList(sections.map((prop, key) => <SectionButton 
      label={prop.label} 
      icon={prop.icon} 
      url={prop.url} 
      key={key}
    />))
  }, [sections])

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
          {sectionsList}
        </div>
        <div className={"home-page-notifications"}>
          
        </div>
      </div>
    </div>
  )
}
