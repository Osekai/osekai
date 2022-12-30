function useCustomThemeForTool(accent, accentDark) {
    document.getElementsByClassName("tools__tool")[0].innerHTML += `<style>.tools__tool {` + generateCustomThemeVars(accent, accentDark) + `}</style>`
}