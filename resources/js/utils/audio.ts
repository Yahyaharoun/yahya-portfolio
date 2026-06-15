export const playSuccessSound = () => {
  try {
    // A simple tiny success "ding" in base64 (MP3 format)
    const audio = new Audio('/sounds/success.mp3')
    audio.play().catch(e => {
      console.warn('Audio playback failed or was blocked by browser:', e)
    })
  } catch (err) {
    console.error('Audio API not supported or failed', err)
  }
};
