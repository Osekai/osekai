import { Gradient } from '/global/js/graphics/mesh_gradient.js'


setTimeout(() => {
    const gradient = new Gradient()
    gradient.amp = 400
    // Call `initGradient` with the selector to your canvas
    gradient.initGradient('#gradient-canvas')    
}, 500);

