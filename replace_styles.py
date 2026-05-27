import os

files = [r'C:\xampp\htdocs\showroom-oto\Customer\about.php', r'C:\xampp\htdocs\showroom-oto\Customer\booking.php']

for file in files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()

    # Font changes
    content = content.replace('Playfair Display', 'Orbitron')
    content = content.replace('Playfair+Display:wght@400;500;600;700', 'Orbitron:wght@400;500;600;700;800;900')

    # CSS / HTML Class Colors
    content = content.replace('background-color: #050505', 'background-color: #ffffff')
    content = content.replace('color: #e5e2e1', 'color: #0f172a')
    content = content.replace('#050505', '#ffffff')
    content = content.replace('rgba(5, 5, 5, 1)', 'rgba(255, 255, 255, 1)')
    content = content.replace('rgba(5,5,5,0.5)', 'rgba(255,255,255,0.8)')
    content = content.replace('rgba(5,5,5,0.82)', 'rgba(255,255,255,0.9)')
    content = content.replace('rgba(26, 26, 26, 0.45)', 'rgba(255, 255, 255, 0.6)')
    content = content.replace('rgba(26, 26, 26, 0.4)', 'rgba(255, 255, 255, 0.6)')
    content = content.replace('rgba(32, 32, 32, 0.65)', 'rgba(255, 255, 255, 0.9)')
    content = content.replace('rgba(20, 20, 20, 0.98)', 'rgba(255, 255, 255, 0.98)')
    
    # Tailwind Config Colors
    content = content.replace('"background": "#131313"', '"background": "#ffffff"')
    content = content.replace('"surface": "#131313"', '"surface": "#ffffff"')
    content = content.replace('"surface-dim": "#131313"', '"surface-dim": "#f8fafc"')
    content = content.replace('"surface-container-lowest": "#0e0e0e"', '"surface-container-lowest": "#ffffff"')
    content = content.replace('"surface-container-low": "#1c1b1b"', '"surface-container-low": "#f8fafc"')
    content = content.replace('"surface-container": "#201f1f"', '"surface-container": "#f1f5f9"')
    content = content.replace('"surface-container-high": "#2a2a2a"', '"surface-container-high": "#e2e8f0"')
    content = content.replace('"surface-container-highest": "#353534"', '"surface-container-highest": "#cbd5e1"')
    
    content = content.replace('"on-surface": "#e5e2e1"', '"on-surface": "#0f172a"')
    content = content.replace('"on-background": "#e5e2e1"', '"on-background": "#0f172a"')
    content = content.replace('"on-surface-variant": "#d1c5b4"', '"on-surface-variant": "#475569"')
    content = content.replace('"on-secondary-container": "#b7b5b4"', '"on-secondary-container": "#334155"')
    content = content.replace('"outline-variant": "#4e4639"', '"outline-variant": "#cbd5e1"')

    # Watermark text and text-white/70
    content = content.replace('rgba(255, 255, 255, 0.025)', 'rgba(0, 0, 0, 0.05)')
    content = content.replace('text-white/70', 'text-black/10')
    content = content.replace('rgba(255,255,255,0.1)', 'rgba(0,0,0,0.1)')
    content = content.replace('rgba(255,255,255,0.05)', 'rgba(0,0,0,0.05)')
    content = content.replace('rgba(255, 255, 255, 0.04)', 'rgba(0, 0, 0, 0.04)')
    content = content.replace('rgba(255, 255, 255, 0.08)', 'rgba(0, 0, 0, 0.08)')
    
    # Specific elements in booking.php
    content = content.replace('background: #131313 !important', 'background: #ffffff !important')
    content = content.replace('color: #e5e2e1 !important', 'color: #0f172a !important')
    
    # Fix dark class
    content = content.replace('class="dark"', 'class="light"')

    with open(file, 'w', encoding='utf-8') as f:
        f.write(content)

print("Replaced styles successfully")
