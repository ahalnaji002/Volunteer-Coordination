import { useCallback, useEffect, useRef, useState } from 'react'
import { useLocation } from 'react-router-dom'

export function useTransientFeedback(initialValue, duration = 5000) {
  const initialValueRef = useRef(initialValue)
  const [feedback, setFeedback] = useState(initialValueRef.current)
  const { pathname } = useLocation()
  const clearFeedback = useCallback(() => setFeedback(initialValueRef.current), [])
  const hasMessage = typeof feedback === 'string' ? Boolean(feedback) : Boolean(feedback?.message)

  useEffect(() => {
    if (!hasMessage) return undefined
    const timeout = window.setTimeout(clearFeedback, duration)
    return () => window.clearTimeout(timeout)
  }, [clearFeedback, duration, hasMessage, feedback])

  useEffect(() => {
    clearFeedback()
  }, [clearFeedback, pathname])

  return [feedback, setFeedback, clearFeedback]
}
