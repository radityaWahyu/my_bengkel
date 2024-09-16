import{d as _,r as b,o as a,c as e,a as t,t as u,n as k,b as i,u as o,f as r,i as l,w as y,l as v,F as x,y as w,g as h,p as $,Z as C,e as D}from"./app-CB_Q1yd6.js";import{E as T}from"./empty-cart-CKT4-nTG.js";import{L as B}from"./login_two-yA2lVMnP.js";import{C as F,R as N}from"./refresh-cw-DpWDELoH.js";import{C as I}from"./Header.vue_vue_type_script_setup_true_lang-CsIiL-Yf.js";import{X as L}from"./x-DWl-GEay.js";import{_ as S}from"./Frontend.vue_vue_type_script_setup_true_lang-Dt8U8-RV.js";/* empty css            */import"./createLucideIcon-BsoM3zrU.js";import"./Input.vue_vue_type_script_setup_true_lang-D54lCQEL.js";import"./index-C2XJz47D.js";import"./shadcn-BWvbMO9Q.js";import"./bundle-mjs-Dk7hgz7r.js";import"./move-left-p8JgShLV.js";import"./circle-user-Dao3zDS1.js";import"./Footer.vue_vue_type_script_setup_true_lang-CfOjN4Eg.js";const V={key:0,class:"rounded-lg border border-gray-200 bg-white p-3 shadow-sm"},A=w('<div class="space-y-1"><div class="w-[300px] animate-pulse"><div class="h-2 bg-gray-200 rounded-full mb-2.5"></div></div><div class="grid grid-cols-[60%_40%] items-center"><div><div class="animate-pulse"><div class="h-2 bg-gray-200 rounded-full w-1/2 mb-2.5"></div></div><h2 class="animate-pulse"><div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-48 mb-4"></div></h2></div><div class="divide-x divide-gray-200 w-full flex items-center"><div class="grow py-3 px-4 text-center"><div class="animate-pulse"><div class="h-2 bg-gray-200 rounded-full mb-2.5"></div></div><div class="text-lg font-semibold"><div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 mb-4"></div></div></div><div class="grow py-3 px-4 text-center"><div class="animate-pulse"><div class="h-2 bg-gray-200 rounded-full mb-2.5"></div></div><div class="text-lg font-semibold"><div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 mb-4"></div></div></div></div></div><div class="w-full flex items-center justify-end py-4 gap-4"><div class="h-5 bg-gray-200 rounded-full dark:bg-gray-700 w-48 mb-4"></div><div class="h-5 bg-gray-200 rounded-full dark:bg-gray-700 w-48 mb-4"></div></div></div>',1),E=[A],P={key:1,class:"rounded-lg border border-gray-200 bg-white p-3 shadow-sm"},R={class:"space-y-1"},j={class:"flex items-center gap-3"},z={class:"font-normal text-[11px]"},M={class:"grid grid-row-2 lg:grid-cols-[60%_40%] items-center gap-2 lg:gap-0"},U={class:"text-center lg:text-left"},q={class:"text-xs font-semibold"},H={class:"font-semibold text-lg text-muted-foreground"},J={class:"divide-x divide-gray-200 w-full flex items-center"},K={class:"grow py-1 text-center"},X=t("p",{class:"text-xs font-medium text-muted-foreground"}," Jumlah produk ",-1),Z={class:"text-lg font-semibold"},G={class:"grow py-1 text-center"},O=t("p",{class:"text-xs font-medium text-muted-foreground"}," Total : ",-1),Q={class:"text-lg font-semibold"},W={class:"w-full py-2 flex items-center justify-between flex-wrap gap-3 lg:gap-0"},Y={key:0,class:"flex items-center gap-2"},tt={key:1},st={key:2,class:"flex items-center gap-2"},at={class:"flex items-center justify-end gap-2 flex-wrap"},et=["disabled"],nt={key:0},ot={key:1,class:"flex items-center gap-3"},it=_({__name:"TransactionCard",props:{transaction:{},loading:{type:Boolean,default:!1}},emits:["cancel"],setup(p,{emit:c}){const g=p,n=c,f=s=>new Intl.NumberFormat("en-ID",{style:"currency",currency:"IDR",maximumFractionDigits:0}).format(s),d=b(!1),m=()=>{x.put(route("frontend.transaction.cancel",g.transaction.id),{},{onStart:()=>d.value=!0,onSuccess:()=>{n("cancel",!0)},onError:s=>console.log("error"),onFinish:()=>d.value=!1})};return(s,xt)=>s.loading?(a(),e("div",V,E)):(a(),e("div",P,[t("div",R,[t("div",j,[t("p",z," Dipesan tanggal "+u(s.transaction.created_at),1)]),t("div",M,[t("div",U,[t("p",q," Unit layanan "+u(s.transaction.shop),1),t("h2",H," No "+u(s.transaction.transaction_code),1)]),t("div",J,[t("div",K,[X,t("h3",Z,u(s.transaction.product_count)+" Item ",1)]),t("div",G,[O,t("h3",Q,u(f(s.transaction.total)),1)])])]),t("div",W,[t("div",{class:k([{"bg-gray-100 text-gray-900":s.transaction.status==="batal","bg-yellow-300 text-yellow-900":s.transaction.status==="pesan","bg-green-300 text-green-900":s.transaction.status==="proses"},"capitalize focus:outline-none font-medium rounded text-xs px-4 py-2 w-full lg:w-auto"])},[s.transaction.status==="pesan"?(a(),e("span",Y,[i(o(F),{class:"w-4 h-4"}),r(" Menunggu di proses.. ")])):l("",!0),s.transaction.status==="batal"?(a(),e("span",tt,"Pesanan Dibatalkan")):l("",!0),s.transaction.status==="proses"?(a(),e("span",st,[i(o(N),{class:"w-3 h-3 animate-spin"}),r(" Pesanan sedang di proses ")])):l("",!0)],2),t("div",at,[i(o(v),{href:s.route("frontend.transaction.detail",s.transaction.id),as:"button",class:"flex items-center gap-2 py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 w-full lg:w-auto"},{default:y(()=>[i(o(I),{class:"w-5 h-5"}),r(" Detail Transaksi ")]),_:1},8,["href"]),s.transaction.status==="pesan"?(a(),e("button",{key:0,type:"button",class:"focus:outline-none text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:focus:ring-yellow-900 w-full lg:w-auto",onClick:m,disabled:d.value},[d.value?(a(),e("span",nt," Membatalkan Pemesanan... ")):(a(),e("span",ot,[i(o(L),{class:"w-5 h-5"}),r(" Batalkan Pemesanan ")]))],8,et)):l("",!0)])])])]))}}),rt={class:"lg:container p-4 min-h-[600px]"},lt={class:"lg:w-3/4 mx-auto space-y-4"},dt=t("h2",{class:"text-lg font-semibold text-gray-900 dark:text-white"}," Daftar Transaksi ",-1),ct={key:0,class:"space-y-4"},ut=t("div",null,[t("blockquote",{class:"my-6 border-l-2 pl-6 italic text-sm"},[t("strong",null,"Keterangan :"),r(" Halaman ini adalah tampilan daftar transaksi yang anda lakukan di aplikasi kami. ")])],-1),gt={key:1,class:"w-full"},mt=["src"],pt=t("h2",{class:"scroll-m-20 border-b pb-2 text-xl font-semibold tracking-tight transition-colors text-center"}," Tidak terdapat transaksi pada akun anda. ",-1),ft=t("p",{class:"text-center text-muted-foreground"}," Silahkan membuat transaksi pemesanan terlebih dahulu dengan memilih produk yang tersedia dan mengirim pesanan. ",-1),ht={key:2,class:"w-full"},_t=["src"],bt=t("h2",{class:"scroll-m-20 border-b pb-2 text-xl font-semibold tracking-tight transition-colors text-center"}," Anda harus LogIn terlebih dahulu agar dapat melihat daftar transaksi anda. ",-1),yt={class:"text-center text-muted-foreground"},vt={layout:S},Rt=_({...vt,__name:"Transactions",props:{transactions:{},params:{}},setup(p){const c=b(!1),g=()=>{x.reload({only:["transactions","params"],onStart:()=>c.value=!0,onFinish:()=>c.value=!1})};return(n,f)=>(a(),e(h,null,[i(o(C),{title:"Daftar Transaksi"}),t("div",rt,[t("div",lt,[dt,n.transactions!==null&&n.transactions.data.length>0?(a(),e("div",ct,[ut,(a(!0),e(h,null,$(n.transactions.data,(d,m)=>(a(),D(it,{key:m,transaction:d,loading:c.value,onCancel:g},null,8,["transaction","loading"]))),128))])):l("",!0),n.transactions!==null&&n.transactions.data.length===0?(a(),e("div",gt,[t("img",{src:o(T),alt:"empty_cart",class:"w-80 h-80 mx-auto"},null,8,mt),pt,ft])):l("",!0),n.transactions===null?(a(),e("div",ht,[t("img",{src:o(B),alt:"empty_cart",class:"w-80 h-80 mx-auto"},null,8,_t),bt,t("p",yt,[r(" Apabila belum punya akun silahkan mendaftar terlebih dahulu dengan mengklik tombol dibawah. "),i(o(v),{href:n.route("frontend.register"),as:"button",class:"mt-4 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-5 py-2.5 text-center"},{default:y(()=>[r(" Daftar Akun Baru ")]),_:1},8,["href"])])])):l("",!0)])])],64))}});export{Rt as default};
