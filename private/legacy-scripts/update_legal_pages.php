<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$privacy = <<<'HTML'
<div class="legal-highlight">
  <div>
    <strong>Your privacy comes first</strong>
    AutoScriptHub is a digital marketplace. We only collect the information needed to create your account, process orders, deliver downloads, and keep the platform secure.
  </div>
</div>

<h2 id="information-we-collect">Information We Collect</h2>
<p>We may collect the following categories of information when you use auto-script-hub.com:</p>
<ul>
  <li><strong>Account details:</strong> name, email address, username, and password.</li>
  <li><strong>Order information:</strong> purchase history, licenses selected, and download activity.</li>
  <li><strong>Technical data:</strong> IP address, browser type, device information, and usage analytics.</li>
  <li><strong>Support communications:</strong> messages you send to our team, including contact form submissions and support tickets.</li>
</ul>

<h2 id="how-we-use-information">How We Use Your Information</h2>
<p>We use personal data to operate and improve AutoScriptHub, including to:</p>
<ul>
  <li>Process payments and deliver digital products.</li>
  <li>Provide customer support and account assistance.</li>
  <li>Send important service updates, such as order confirmations and security notices.</li>
  <li>Protect the platform against fraud, abuse, and unauthorized access.</li>
  <li>Improve website performance, product discovery, and user experience.</li>
</ul>

<h2 id="payment-processing">Payment Processing</h2>
<p>We do <strong>not</strong> store full payment card details on our servers. Payments are processed securely by trusted third-party providers such as PayPal, Stripe, Dodo Payments, and Whop. Each provider handles card data according to its own privacy and security standards.</p>

<h2 id="sharing-information">Sharing Your Information</h2>
<p>We do not sell, rent, or trade your personal information. We may share limited data only when necessary with:</p>
<ul>
  <li>Payment processors and fraud-prevention partners.</li>
  <li>Hosting, email, analytics, and infrastructure providers that help us run the website.</li>
  <li>Law enforcement or regulatory authorities when required by applicable law.</li>
</ul>

<h2 id="cookies">Cookies & Analytics</h2>
<p>AutoScriptHub uses cookies and similar technologies to remember preferences, keep you signed in, measure traffic, and improve the marketplace experience. You can control cookies through your browser settings, although some features may not work correctly if cookies are disabled.</p>

<h2 id="data-security">How We Protect Your Data</h2>
<p>We apply reasonable technical and organizational safeguards, including encrypted connections (SSL), access controls, and secure hosting practices. While no online service can guarantee absolute security, we work continuously to protect customer data.</p>

<h2 id="your-rights">Your Rights</h2>
<p>Depending on your location, you may have the right to:</p>
<ul>
  <li>Request access to the personal data we hold about you.</li>
  <li>Update or correct inaccurate account information.</li>
  <li>Request deletion of your account and associated personal data.</li>
  <li>Opt out of non-essential marketing communications.</li>
</ul>
<p>To make a privacy request, contact us using the details below and we will respond within a reasonable timeframe.</p>

<h2 id="data-retention">Data Retention</h2>
<p>We retain personal information only for as long as needed to provide our services, comply with legal obligations, resolve disputes, and enforce our agreements.</p>

<h2 id="contact-us">Contact Us</h2>
<p>If you have questions about this Privacy Policy or how your data is handled, contact us at:</p>
<p><strong>Email:</strong> <a href="mailto:support@auto-script-hub.com">support@auto-script-hub.com</a><br>
<strong>Website:</strong> <a href="https://auto-script-hub.com">auto-script-hub.com</a></p>

<h2 id="consent">Consent</h2>
<p>By creating an account, purchasing a product, or continuing to use AutoScriptHub, you acknowledge that you have read and agree to this Privacy Policy.</p>
HTML;

$terms = <<<'HTML'
<div class="legal-highlight">
  <div>
    <strong>Please read carefully</strong>
    These Terms govern your use of AutoScriptHub, including browsing, purchasing digital products, creating an account, and selling on our marketplace.
  </div>
</div>

<h2 id="acceptance">Acceptance of Terms</h2>
<p>By accessing or using <a href="https://auto-script-hub.com">auto-script-hub.com</a>, you agree to these Terms & Conditions. If you do not agree, please do not use the website or our services.</p>

<h2 id="use-of-website">Use of the Website</h2>
<p>You agree to use AutoScriptHub only for lawful purposes and in a manner that does not infringe the rights of others, disrupt the platform, or attempt to compromise its security or availability.</p>

<h2 id="products-services">Products & Services</h2>
<p>AutoScriptHub provides digital products such as SaaS scripts, source code, plugins, templates, and related downloadable materials. All products are delivered electronically unless otherwise stated on the product page.</p>

<h2 id="accounts">Account Registration</h2>
<p>Some features require an account. You are responsible for maintaining the confidentiality of your login credentials and for all activity that occurs under your account. Please notify us immediately if you suspect unauthorized access.</p>

<h2 id="payments-delivery">Payments & Delivery</h2>
<p>Prices may be displayed in USD or other supported currencies. Payments are processed through secure third-party payment providers. After successful payment, digital products are typically made available for download from your account or delivered by email.</p>

<h2 id="refunds">Refund Policy</h2>
<p>Unless otherwise stated on a product page, purchases are generally final because digital goods cannot be returned once delivered. Refunds may be considered only where a verified technical issue cannot be resolved by our support team.</p>

<h2 id="intellectual-property">Intellectual Property</h2>
<p>All marketplace content, branding, and platform features remain the property of AutoScriptHub or their respective owners. Product licenses are granted according to the license type selected at checkout. Redistribution, resale, or unauthorized modification may be prohibited unless explicitly allowed by the product license.</p>

<h2 id="prohibited-activities">Prohibited Activities</h2>
<p>You agree not to:</p>
<ul>
  <li>Use the platform for unlawful, fraudulent, or abusive activity.</li>
  <li>Attempt to access another user's account, files, or private data.</li>
  <li>Upload malware, spam, or harmful code.</li>
  <li>Circumvent licensing restrictions or resell products without permission.</li>
</ul>

<h2 id="disclaimer">Disclaimer</h2>
<p>AutoScriptHub provides digital products and marketplace services on an "as is" and "as available" basis. We do not guarantee uninterrupted access, error-free operation, or that every product will meet your specific business requirements.</p>

<h2 id="limitation-liability">Limitation of Liability</h2>
<p>To the fullest extent permitted by law, AutoScriptHub shall not be liable for indirect, incidental, special, or consequential damages arising from your use of the website, purchased products, or inability to access services.</p>

<h2 id="modifications">Changes to These Terms</h2>
<p>We may update these Terms from time to time. Revised terms will be posted on this page with an updated effective date. Continued use of the website after changes are published constitutes acceptance of the updated Terms.</p>

<h2 id="governing-law">Governing Law</h2>
<p>These Terms are governed by the laws applicable to the operation of AutoScriptHub, without regard to conflict-of-law principles.</p>

<h2 id="contact-us">Contact Us</h2>
<p>If you have questions about these Terms, please contact us at:</p>
<p><strong>Email:</strong> <a href="mailto:support@auto-script-hub.com">support@auto-script-hub.com</a><br>
<strong>Website:</strong> <a href="https://auto-script-hub.com">auto-script-hub.com</a></p>
HTML;

DB::table('pages')->where('page_slug', 'privacy-policy')->update(['page_desc' => $privacy]);
DB::table('pages')->where('page_slug', 'terms-and-conditions')->update(['page_desc' => $terms]);

$faq = <<<'HTML'
<div class="legal-highlight">
  <div>
    <strong>Answers before you buy</strong>
    Browse the most common questions about products, checkout, downloads, licensing, and support on AutoScriptHub.
  </div>
</div>

<div class="legal-faq-list">
  <div class="legal-faq-item" id="faq-products">
    <button type="button" class="legal-faq-question" aria-expanded="false">
      <span>What kind of products do you offer?</span>
    </button>
    <div class="legal-faq-answer">
      <p>AutoScriptHub offers premium digital products including SaaS scripts, web applications, AI tools, plugins, templates, and other downloadable software ready for installation and use.</p>
    </div>
  </div>

  <div class="legal-faq-item" id="faq-purchase">
    <button type="button" class="legal-faq-question" aria-expanded="false">
      <span>How do I purchase a product?</span>
    </button>
    <div class="legal-faq-answer">
      <p>Open the product page, choose your license option, click <strong>Buy Now</strong> or add the item to cart, then complete secure checkout. After payment, your download becomes available in your account.</p>
    </div>
  </div>

  <div class="legal-faq-item" id="faq-delivery">
    <button type="button" class="legal-faq-question" aria-expanded="false">
      <span>How will I receive my product after purchase?</span>
    </button>
    <div class="legal-faq-answer">
      <p>Digital delivery is instant. Once payment is confirmed, you can access your purchase from your account dashboard. You may also receive an order confirmation email with access details.</p>
    </div>
  </div>

  <div class="legal-faq-item" id="faq-installation">
    <button type="button" class="legal-faq-question" aria-expanded="false">
      <span>Can I get help with installation?</span>
    </button>
    <div class="legal-faq-answer">
      <p>Yes. Many products include installation support depending on the license and product type. If you need help, contact our support team with your order details and we will guide you through setup.</p>
    </div>
  </div>

  <div class="legal-faq-item" id="faq-refunds">
    <button type="button" class="legal-faq-question" aria-expanded="false">
      <span>Do you offer refunds?</span>
    </button>
    <div class="legal-faq-answer">
      <p>Because we sell digital products, purchases are generally final. Refunds may be considered only in rare cases where a verified critical issue cannot be resolved by support. Please read our <a href="/refund-policy">Refund Policy</a> for full details.</p>
    </div>
  </div>

  <div class="legal-faq-item" id="faq-payments">
    <button type="button" class="legal-faq-question" aria-expanded="false">
      <span>Is my payment information secure?</span>
    </button>
    <div class="legal-faq-answer">
      <p>Yes. Payments are processed through trusted third-party providers such as PayPal, Stripe, Dodo Payments, and Whop. AutoScriptHub does not store your full card details on our servers.</p>
    </div>
  </div>

  <div class="legal-faq-item" id="faq-licensing">
    <button type="button" class="legal-faq-question" aria-expanded="false">
      <span>Can I use a product on multiple websites?</span>
    </button>
    <div class="legal-faq-answer">
      <p>Usage depends on the license selected at checkout. Please review the product page and license terms before purchasing to confirm whether single-site or extended usage is included.</p>
    </div>
  </div>

  <div class="legal-faq-item" id="faq-support">
    <button type="button" class="legal-faq-question" aria-expanded="false">
      <span>What if I have a problem after purchase?</span>
    </button>
    <div class="legal-faq-answer">
      <p>Contact our support team with your order ID, product name, and a clear description of the issue. We aim to respond quickly and help resolve technical problems whenever possible.</p>
    </div>
  </div>

  <div class="legal-faq-item" id="faq-contact">
    <button type="button" class="legal-faq-question" aria-expanded="false">
      <span>How can I contact support?</span>
    </button>
    <div class="legal-faq-answer">
      <p>You can reach us by email at <a href="mailto:support@auto-script-hub.com">support@auto-script-hub.com</a> or through our <a href="/contact">contact page</a>.</p>
    </div>
  </div>
</div>
HTML;

$refund = <<<'HTML'
<div class="legal-highlight">
  <div>
    <strong>Digital products are delivered instantly</strong>
  Because of that, purchases are usually final. We still want every customer to have a fair experience, so refunds may be reviewed when a verified product issue cannot be resolved.
  </div>
</div>

<h2 id="overview">Overview</h2>
<p>Thank you for shopping at AutoScriptHub. We work hard to provide high-quality digital products and reliable support. Please read this Refund Policy carefully before completing your purchase.</p>

<h2 id="general-policy">General Policy</h2>
<p>Due to the nature of digital goods, <strong>all sales are generally final</strong> once a product has been purchased, downloaded, or accessed. This helps protect creators and prevent unauthorized redistribution.</p>

<div class="legal-cards">
  <div class="legal-card legal-card-success">
    <h3>Refunds may be considered if</h3>
    <ul>
      <li>There is a critical issue that prevents the product from working.</li>
      <li>You contacted support and allowed us to troubleshoot the problem.</li>
      <li>Our team confirms the issue cannot be resolved within a reasonable time.</li>
    </ul>
  </div>
  <div class="legal-card legal-card-danger">
    <h3>Refunds are not issued for</h3>
    <ul>
      <li>Change of mind after purchase.</li>
      <li>Expectations that differ from the product description.</li>
      <li>Hosting, server, or environment compatibility issues on your side.</li>
      <li>Problems caused by third-party modifications or custom changes.</li>
    </ul>
  </div>
</div>

<h2 id="refund-process">Refund Request Process</h2>
<div class="legal-steps">
  <div class="legal-step">
    <span class="legal-step-num">1</span>
    <div>
      <strong>Contact support first</strong>
      Email us with your order ID, product name, and a detailed description of the issue.
    </div>
  </div>
  <div class="legal-step">
    <span class="legal-step-num">2</span>
    <div>
      <strong>Allow troubleshooting</strong>
      Our team will review the case and attempt to resolve the problem with you.
    </div>
  </div>
  <div class="legal-step">
    <span class="legal-step-num">3</span>
    <div>
      <strong>Case-by-case review</strong>
      If the issue cannot be fixed, we may approve a refund depending on the circumstances.
    </div>
  </div>
</div>

<h2 id="support-assistance">Support Assistance</h2>
<p>Most order issues can be solved without a refund. Please contact support before requesting a chargeback or payment dispute. We are here to help with installation guidance, access problems, and technical troubleshooting.</p>

<h2 id="acknowledgement">Customer Acknowledgement</h2>
<p>By purchasing from AutoScriptHub, you acknowledge that digital products are non-refundable except in the limited situations described above.</p>

<h2 id="contact-us">Contact Us</h2>
<p>For refund questions or order support, contact:</p>
<p><strong>Email:</strong> <a href="mailto:support@auto-script-hub.com">support@auto-script-hub.com</a><br>
<strong>Website:</strong> <a href="https://auto-script-hub.com">auto-script-hub.com</a></p>
HTML;

DB::table('pages')->where('page_slug', 'faq')->update(['page_desc' => $faq]);
DB::table('pages')->where('page_slug', 'refund-policy')->update(['page_desc' => $refund]);

echo "Legal pages updated successfully.\n";
