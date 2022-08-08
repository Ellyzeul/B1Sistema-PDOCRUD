import "./style.css"
import { BookQuoteCardProp } from "./types"

export const BookQuotesCard = (props: BookQuoteCardProp) => {
  const { userName } = props

  return (
    <div className="book-quote-card">
      <div></div>
      <div className="book-quote-card-header">Inspire-se</div>
      <div className="book-quote-body">
        <div className="book-quote-greeting">Olá {userName}</div>
        <div className="book-quote-content">
          <div>
            <strong>"Daqui pra frente, só pra trás"</strong>
            <br />
            <p>Provérbio popular</p>
          </div>
        </div>
      </div>
    </div>
  )
}
