function useCustomThemeForTool(accent, accentDark) {
    document.getElementsByClassName("tools__tool-container-inner")[0].innerHTML += `<style>.tools__tool-container-inner {${generateCustomThemeVars(accent, accentDark)}}</style>`
}