import { useEffect, useState } from "react"
import api from "../../services/axios"
import "./style.css"
import { BookQuoteCardProp } from "./types"

export const BookQuotesCard = (props: BookQuoteCardProp) => {
  const { userName } = props
  const [quoteInfo, setQuoteInfo] = useState({} as {
    quote: string,
    author: string
  })

  useEffect(() => {
    api.get('/api/quotes/read')
      .then(response => response.data)
      .then(response => {
        const { quote, author } = response

        setQuoteInfo({
          quote: quote,
          author: author
        })
      })
  }, [])

  return (
    <div className="book-quote-card">
      <div></div>
      <div className="book-quote-card-header">Inspire-se</div>
      <div className="book-quote-body">
        <div className="book-quote-greeting">Olá {userName}</div>
        <div className="book-quote-content">
          <div>
            <strong>{quoteInfo.quote ? `"${quoteInfo.quote}"` : null}</strong>
            <br />
            <p>{quoteInfo.author}</p>
          </div>
        </div>
      </div>
    </div>
  )
}
