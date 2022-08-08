import { createContext } from "react";
import { UserData } from "../components/LoginForm/types";

export const UserDataContext = createContext([null as (UserData | null), (prevState: UserData): void => {}])
