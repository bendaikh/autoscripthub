.hv-card-stage { position:relative; }
.hv-card-loader { background:#fff; border:1px solid #e6e9f0; border-radius:16px; padding:1.15rem 1.15rem 1rem; box-shadow:0 8px 24px rgba(15,23,42,.04); }
.hv-card-loader[hidden] { display:none !important; }
.hv-card-loader-label { margin:0 0 .35rem; color:#334155; font-size:.95rem; }
.hv-card-spin { width:48px; height:48px; margin:.85rem auto .9rem; border-radius:50%; border:3px solid #eadcff; border-top-color:#6b46c1; animation:hvCardSpin .8s linear infinite; }
.hv-card-loader-title { margin:0; text-align:center; font-size:1.05rem; font-weight:800; color:#0f172a; }
.hv-card-loader-sub { margin:.28rem 0 .95rem; text-align:center; color:#94a3b8; font-size:.82rem; }
.hv-card-bar { height:4px; border-radius:99px; background:#eceff3; overflow:hidden; margin-bottom:1rem; }
.hv-card-bar span { display:block; height:100%; width:8%; border-radius:99px; background:#6b46c1; animation:hvCardBar 12s ease forwards; }
.hv-card-skel { height:44px; border-radius:10px; margin-bottom:.65rem; background:linear-gradient(90deg,#e7ebf0 25%,#f8f9fb 50%,#e7ebf0 75%); background-size:200% 100%; animation:hvCardShimmer 1.3s linear infinite; }
.hv-card-skel-row { display:flex; gap:.65rem; }
.hv-card-skel-row .hv-card-skel { flex:1; }
.hv-card-wait { display:block; width:100%; margin-top:.35rem; border:0; border-radius:10px; padding:.9rem 1rem; background:#eceaf1; color:#b0aab8; font-weight:700; }
.hv-card-pci { margin:.7rem 0 .15rem; text-align:center; color:#16a34a; font-size:.84rem; font-weight:600; }
.hv-card-pci svg { vertical-align:-2px; margin-right:.2rem; }
.hv-card-fail { display:none; text-align:center; color:#334155; padding:1.5rem .5rem; line-height:1.5; }
.hv-card-fail p { margin:0 0 .85rem; }
.hv-card-loader.is-failed .hv-card-loading { display:none; }
.hv-card-loader.is-failed .hv-card-fail { display:block; }
.hv-card-fail a { color:#6b46c1; font-weight:700; }
.hv-card-stage.is-loading { overflow:hidden; }
.hv-card-stage.is-loading > :not(.hv-card-loader) { position:absolute; left:0; right:0; opacity:0; pointer-events:none; }
.hv-card-stage.is-ready > :not(.hv-card-loader) { opacity:1; transition:opacity .35s ease; }
.hv-card-stage.is-ready .hv-card-loader { position:absolute; inset:0; opacity:0; pointer-events:none; transition:opacity .35s ease; }
@keyframes hvCardSpin { to { transform:rotate(360deg); } }
@keyframes hvCardBar { to { width:90%; } }
@keyframes hvCardShimmer { to { background-position:-200% 0; } }
