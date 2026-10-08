/* Shared print styles: tables and block layout are supported by Dompdf. */
body { color:#243247; font-family:DejaVu Sans,sans-serif; line-height:1.5; }
.document-header { width:100%; margin-bottom:18px; padding-bottom:14px; border-bottom:3px solid #b91c1c; border-collapse:collapse; }
.document-header td { padding:0; border:0; vertical-align:middle; background:transparent; }
.document-header .logo-cell { width:82px; }
.document-logo { width:70px; height:70px; object-fit:cover; }
.document-company { color:#172033; font-size:15px; font-weight:bold; }
.document-address { margin-top:4px; color:#667085; font-size:8px; }
.document-header .document-meta { width:29%; color:#667085; font-size:8px; line-height:1.6; text-align:right; }
.document-kicker { color:#b91c1c; font-size:8px; font-weight:bold; letter-spacing:.7px; text-transform:uppercase; }
.document-title { margin:3px 0 5px; color:#172033; font-size:24px; line-height:1.2; text-align:left; }
.document-subtitle { margin:0 0 18px; color:#667085; font-size:9px; }
.document-footer { position:fixed; right:0; bottom:-27px; left:0; padding-top:7px; border-top:1px solid #d7dce4; color:#667085; font-size:7px; }
.document-footer table { width:100%; border-collapse:collapse; }
.document-footer td { padding:0; border:0; background:transparent; }
.document-footer .page { width:20%; text-align:right; }
.document-page:before { content:counter(page); }
.section-heading,.section-title { page-break-after:avoid; }
thead { display:table-header-group; }
tr { page-break-inside:avoid; }
.data td { word-wrap:break-word; }
.data .amount { white-space:normal; }
