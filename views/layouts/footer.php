<footer style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 4rem 2rem 2rem; margin-top: 5rem; position: relative; overflow: hidden;">
    <!-- Decorative Elements -->
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #3b82f6, #6366f1, #8b5cf6, #3b82f6); background-size: 200% 100%; animation: gradientFlow 3s linear infinite;"></div>
    
    <style>
        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        
        .footer-icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
            animation: float 3s ease-in-out infinite;
        }
        
        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-weight: 500;
        }
        
        .footer-link:hover {
            color: #60a5fa;
            gap: 0.625rem;
        }
        
        .footer-social {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }
        
        .footer-social:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.4);
        }
        
        .footer-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(148, 163, 184, 0.3), transparent);
            margin: 2.5rem 0;
        }
    </style>
    
    <div class="container" style="max-width: 1280px; margin: 0 auto;">
        <!-- Footer Top Section -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem; margin-bottom: 3rem; text-align: left;">
            <!-- Brand Column -->
            <div>
                <div class="footer-icon-wrapper">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1rem; background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    <?php echo APP_NAME; ?>
                </h3>
                <p style="color: #94a3b8; line-height: 1.7; font-size: 0.9375rem; margin-bottom: 1.5rem;">
                    Intelligent dynamic pricing solutions for modern businesses. Optimize your revenue with data-driven insights.
                </p>
                <!-- Social Links -->
                <div style="display: flex; gap: 0.75rem;">
                    <a href="#" class="footer-social" aria-label="Twitter">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/in/teay/" class="footer-social" aria-label="LinkedIn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"></path>
                            <circle cx="4" cy="4" r="2"></circle>
                        </svg>
                    </a>
                    <a href="https://github.com/StephenTeay/dynamic_pricing" class="footer-social" aria-label="GitHub">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 00-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0020 4.77 5.07 5.07 0 0019.91 1S18.73.65 16 2.48a13.38 13.38 0 00-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 005 4.77a5.44 5.44 0 00-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 009 18.13V22"></path>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; color: white; text-transform: uppercase; letter-spacing: 1px;">Quick Links</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 0.75rem;">
                        <a href="<?php echo BASE_URL; ?>/buyer/shop" class="footer-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            Shop Products
                        </a>
                    </li>
                    <li style="margin-bottom: 0.75rem;">
                        <a href="<?php echo BASE_URL; ?>/buyer/orders" class="footer-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                            </svg>
                            My Orders
                        </a>
                    </li>
                    <li style="margin-bottom: 0.75rem;">
                        <a href="<?php echo BASE_URL; ?>/buyer/cart" class="footer-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                            Shopping Cart
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/about" class="footer-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            About Us
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Support -->
            <div>
                <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; color: white; text-transform: uppercase; letter-spacing: 1px;">Support</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 0.75rem;">
                        <a href="<?php echo BASE_URL; ?>/help" class="footer-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            Help Center
                        </a>
                    </li>
                    <li style="margin-bottom: 0.75rem;">
                        <a href="<?php echo BASE_URL; ?>/contact" class="footer-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            Contact Us
                        </a>
                    </li>
                    <li style="margin-bottom: 0.75rem;">
                        <a href="<?php echo BASE_URL; ?>/faq" class="footer-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            FAQ
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/privacy" class="footer-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            Privacy Policy
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div>
                <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; color: white; text-transform: uppercase; letter-spacing: 1px;">Get In Touch</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 1rem; display: flex; align-items: flex-start; gap: 0.75rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <div>
                            <div style="color: #94a3b8; font-size: 0.8125rem; margin-bottom: 0.25rem;">Email</div>
                            <a href="teaycorps@gmail.com" style="color: white; text-decoration: none; font-weight: 600;">admin@dynamic.com</a>
                        </div>
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: flex-start; gap: 0.75rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <div>
                            <div style="color: #94a3b8; font-size: 0.8125rem; margin-bottom: 0.25rem;">Phone</div>
                            <a href="tel:+2348114891459" style="color: white; text-decoration: none; font-weight: 600;">+0808DYNAMIC</a>
                        </div>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <div>
                            <div style="color: #94a3b8; font-size: 0.8125rem; margin-bottom: 0.25rem;">Address</div>
                            <span style="color: white; font-weight: 600; line-height: 1.5;">Nigeria</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Divider -->
        <div class="footer-divider"></div>
        
        <!-- Footer Bottom -->
        <div style="text-align: center;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <p style="margin: 0; font-size: 0.9375rem; color: #cbd5e1; font-weight: 500;">
                    &copy; <?php echo date('Y'); ?> <strong style="color: white;"><?php echo APP_NAME; ?></strong>. All rights reserved.
                </p>
            </div>
            <p style="margin: 0; font-size: 0.875rem; color: #94a3b8; line-height: 1.6;">
                Developed with 
                <span style="color: #ef4444; font-size: 1.125rem; display: inline-block; animation: pulse 1.5s ease-in-out infinite;">❤️</span> 
                for dynamic pricing management.
            </p>
            <div style="margin-top: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 1.5rem; flex-wrap: wrap; font-size: 0.8125rem;">
                <a href="<?php echo BASE_URL; ?>/terms" class="footer-link">Terms of Service</a>
                <span style="color: #475569;">•</span>
                <a href="<?php echo BASE_URL; ?>/privacy" class="footer-link">Privacy Policy</a>
                <span style="color: #475569;">•</span>
                <a href="<?php echo BASE_URL; ?>/cookies" class="footer-link">Cookie Policy</a>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
    </style>
</footer>
</body>
</html>