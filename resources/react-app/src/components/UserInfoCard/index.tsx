import { UserInfoCardProp } from "./types"

export const UserInfoCard = (props: UserInfoCardProp) => {
  const { userData } = props

  return (
    <div className="user-info-card">
      <p>Nome: {userData.name}</p>
      <hr />
      <p>E-mail: {userData.email}</p>
      <hr />
      <p>Ramal: {userData.ramal}</p>
      <hr />
    </div>
  )
}