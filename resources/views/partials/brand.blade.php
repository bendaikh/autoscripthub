{{--
  AutoScriptHub wordmark, used in place of a logo image.
  Options:
    $brandCompact - drop the tagline (mobile / tight spaces)
    $brandLight   - light text, for dark backgrounds such as the footer
--}}
<span class="asb {{ ($brandCompact ?? false) ? 'asb--compact' : '' }} {{ ($brandLight ?? false) ? 'asb--light' : '' }}">
  <span class="asb-mark" aria-hidden="true">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M12 2.2l2.5 6.3 6.3 2.5-6.3 2.5L12 19.8l-2.5-6.3L3.2 11l6.3-2.5L12 2.2z" fill="currentColor"/>
    </svg>
  </span>
  <span class="asb-text">
    <span class="asb-name">AutoScript<span class="asb-name-accent">Hub</span></span>
    <span class="asb-tag">{{ __('Digital Pro Hub') }}</span>
  </span>
</span>
