/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/View/Components/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                status: {
                    pending: '#f59e0b',
                    confirmed: '#10b981',
                    cancelled: '#ef4444',
                    shipped: '#3b82f6',
                    incomplete: '#6b7280',
                },
                dashboard: {
                    DEFAULT: '#f8fafc',
                    'card-bg': '#ffffff',
                    'border': '#e2e8f0',
                    'text-primary': '#1e293b',
                    'text-secondary': '#64748b',
                    'text-muted': '#94a3b8',
                }
            },
            fontSize: {
                'card-heading': '0.95rem',
                'card-value': '1.75rem',
                'card-subtitle': '0.75rem',
            },
            boxShadow: {
                'card': '0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04)',
                'card-hover': '0 4px 12px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04)',
                'sidebar': '4px 0 12px rgba(0,0,0,0.05)',
            }
        },
    },
    plugins: [],
}
