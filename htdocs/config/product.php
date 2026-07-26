<?php
/**
 * Product Layer Configuration — AuraPay
 *
 * Product-specific branding, theme, and feature configuration.
 * The Lending Platform Framework loads this file to skin the white-label app.
 */

declare(strict_types=1);

return [
    'key'           => 'aurapay',
    'name'          => 'AuraPay',
    'tagline'       => 'Fast, fair, and transparent lending — built for you.',
    'description'   => 'AuraPay is a digital lending platform offering personal loans with transparent terms, fair interest rates, and a seamless application experience.',
    'company'       => 'AuraPay Lending Inc.',
    'url'           => 'https://aurapay.ph',

    // Contact
    'support_email'   => 'support@aurapay.ph',
    'support_phone'   => '+63 2 8888 8888',
    'address'         => '12F Aura Tower, 22nd Street, Bonifacio Global City, Taguig, Metro Manila, Philippines',
    'business_hours'  => 'Mon - Fri, 8:00 AM - 5:00 PM (PHT)',

    // Social
    'social' => [
        'facebook'  => 'https://facebook.com/aurapay',
        'twitter'   => 'https://twitter.com/aurapay',
        'instagram' => 'https://instagram.com/aurapay',
        'linkedin'  => 'https://linkedin.com/company/aurapay',
    ],

    // Theme
    'theme' => [
        'primary'        => '#0F4C81', // AuraPay blue
        'primary_dark'   => '#0A3559',
        'primary_light'  => '#1E6BB8',
        'accent'         => '#00B4A6', // teal accent
        'secondary'      => '#FF8C42', // warm orange
        'success'        => '#10B981',
        'warning'        => '#F59E0B',
        'error'          => '#EF4444',
        'info'           => '#3B82F6',
        'bg'             => '#F8FAFC',
        'bg_alt'         => '#F1F5F9',
        'surface'        => '#FFFFFF',
        'text'           => '#1E293B',
        'text_muted'     => '#64748B',
        'border'         => '#E2E8F0',
        'font'           => "'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif",
        'font_heading'   => "'Poppins', 'Inter', sans-serif",
        'radius'         => '10px',
        'shadow'         => '0 4px 14px rgba(15, 76, 129, 0.08)',
        'logo_text'      => 'AuraPay',
    ],

    // Features
    'features' => [
        'otp_verification'   => true,
        'credit_evaluation'  => true,
        'document_upload'    => true,
        'loan_application'   => true,
        'repayment_tracking' => true,
        'notifications'      => true,
        'timeline'           => true,
        'admin_portal'       => true,
        'reports'            => true,
        'audit_logs'         => true,
        'maintenance_mode'   => true,
    ],

    // Loan defaults
    'loan' => [
        'min_amount'         => 5000,
        'max_amount'         => 50000,
        'min_term'           => 3,
        'max_term'           => 12,
        'default_interest_rate' => 3.5, // % per month
        'default_processing_fee' => 150,
        'late_penalty_rate'  => 2.0, // % per week on overdue
        'grace_days'         => 3,
    ],

    // Credit evaluation weights
    'credit' => [
        'min_score'          => 300,
        'max_score'          => 850,
        'approval_threshold' => 580,
        'weights' => [
            'employment'    => 25,
            'income'        => 30,
            'documents'     => 20,
            'identity'      => 15,
            'history'        => 10,
        ],
    ],

    // Landing page content
    'landing' => [
        'hero_title'       => 'Borrow with confidence.',
        'hero_subtitle'    => 'AuraPay offers fast, fair, and fully transparent personal loans — no hidden fees, no surprises. Apply in minutes, get a decision fast.',
        'hero_cta_primary'   => 'Apply now',
        'hero_cta_secondary'  => 'How it works',
        'stats' => [
            ['value' => '50,000+', 'label' => 'Loans disbursed'],
            ['value' => '₱2.4B',   'label' => 'Total funded'],
            ['value' => '4.8/5',   'label' => 'Customer rating'],
            ['value' => '< 24h',   'label' => 'Avg. approval time'],
        ],
        'features' => [
            [
                'icon'  => 'shield-check',
                'title' => 'Transparent terms',
                'text'  => 'No hidden fees. Every charge, rate, and due date is shown upfront before you sign.',
            ],
            [
                'icon'  => 'zap',
                'title' => 'Fast approval',
                'text'  => 'Apply in minutes with our fully digital flow. Most applications get a decision within 24 hours.',
            ],
            [
                'icon'  => 'lock',
                'title' => 'Bank-grade security',
                'text'  => 'Your data is encrypted and protected. We never sell your information to third parties.',
            ],
            [
                'icon'  => 'wallet',
                'title' => 'Flexible repayment',
                'text'  => 'Choose a term that fits your budget. Pay via bank, e-wallet, or over the counter.',
            ],
            [
                'icon'  => 'user-check',
                'title' => 'Fair evaluation',
                'text'  => 'Our credit assessment looks at the whole picture — not just a credit score.',
            ],
            [
                'icon'  => 'headset',
                'title' => 'Human support',
                'text'  => 'Real people, ready to help. Reach us by email, phone, or chat during business hours.',
            ],
        ],
        'steps' => [
            ['title' => 'Create your account', 'text' => 'Sign up and verify your identity with a one-time password.'],
            ['title' => 'Complete your profile', 'text' => 'Tell us about your employment, income, and upload your documents.'],
            ['title' => 'Get evaluated', 'text' => 'Our system assesses your creditworthiness in seconds.'],
            ['title' => 'Apply for a loan', 'text' => 'Choose your amount and term, then submit your application.'],
            ['title' => 'Get funded', 'text' => 'Once approved and disbursed, funds are sent to your account.'],
        ],
        'testimonials' => [
            ['name' => 'Maria Santos',    'location' => 'Quezon City', 'text' => 'The process was so easy and fast. I got my loan in less than a day. Highly recommended!'],
            ['name' => 'Juan Dela Cruz',  'location' => 'Manila',       'text' => 'Transparent and fair. No hidden charges unlike other lenders I have tried.'],
            ['name' => 'Andrea Reyes',    'location' => 'Cebu',         'text' => 'The app is clean and easy to use. Customer support was very helpful.'],
        ],
        'faq' => [
            ['q' => 'How much can I borrow?', 'a' => 'You can borrow from ₱5,000 up to ₱50,000 depending on your credit evaluation.'],
            ['q' => 'How long does approval take?', 'a' => 'Most applications are reviewed within 24 hours during business days.'],
            ['q' => 'What documents do I need?', 'a' => 'A valid government-issued ID, proof of income, proof of billing, and a selfie for identity verification.'],
            ['q' => 'How do I receive my loan?', 'a' => 'Once approved and disbursed, the funds are transferred to your nominated bank or e-wallet account.'],
            ['q' => 'Is my data safe?', 'a' => 'Yes. We use bank-grade encryption and never sell your personal information.'],
        ],
    ],
];
