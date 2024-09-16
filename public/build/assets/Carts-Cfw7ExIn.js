import{d as $,T as S,r as f,j as q,o as a,c as r,a as t,u as o,f as d,t as h,i as p,z as F,A as K,W as D,b as y,F as _,g as w,p as E,w as B,Z as N,e as T,l as I}from"./app-CB_Q1yd6.js";import{E as L}from"./empty-cart-CKT4-nTG.js";import{L as V}from"./login_two-yA2lVMnP.js";import{N as z}from"./no-image-DVr5fu4Z.js";import{T as A}from"./trash-CN3VjTEs.js";import{_ as P}from"./Frontend.vue_vue_type_script_setup_true_lang-Dt8U8-RV.js";/* empty css            */import"./createLucideIcon-BsoM3zrU.js";import"./Header.vue_vue_type_script_setup_true_lang-CsIiL-Yf.js";import"./Input.vue_vue_type_script_setup_true_lang-D54lCQEL.js";import"./index-C2XJz47D.js";import"./shadcn-BWvbMO9Q.js";import"./bundle-mjs-Dk7hgz7r.js";import"./move-left-p8JgShLV.js";import"./circle-user-Dao3zDS1.js";import"./Footer.vue_vue_type_script_setup_true_lang-CfOjN4Eg.js";const M={class:"rounded-sm border border-gray-200 bg-white p-3 shadow-sm"},Q={class:"grid lg:grid-cols-[40%_35%_25%] items-center lg:gap-0 gap-2 px-1"},R={class:"flex items-center gap-2"},H={class:"h-12 w-14 overflow-hidden rounded-sm"},O=["src","alt"],U={href:"#",class:"text-sm font-medium text-gray-900 hover:underline dark:text-white"},W={key:0,class:"ml-2 text-xs text-blue-500"},Z={key:1,class:"ml-2 text-xs text-blue-500"},G={class:"text-muted-foreground text-xs"},J={class:"flex items-center justify-between lg:justify-around"},X={class:"text-sm font-medium text-gray-900 text-left"},Y=t("p",{class:"text-xs text-muted-foreground font-medium"}," Harga Satuan ",-1),tt={class:"flex items-center rounded border border-gray-200"},et=["disabled"],st=["disabled"],at=["disabled"],ot={class:"flex items-center justify-between lg:justify-around"},nt={class:"text-sm font-semibold text-gray-900 dark:text-white text-left"},rt=t("p",{class:"text-xs text-muted-foreground font-medium"}," Sub Total ",-1),it={class:"text-center content-center"},lt=["disabled"],ct=$({__name:"ProductCart",props:{cart:{}},emits:["deleted"],setup(x,{emit:l}){const i=x,g=l,s=S({qty:i.cart.quantity}),u=n=>new Intl.NumberFormat("en-ID",{style:"currency",currency:"IDR",maximumFractionDigits:0}).format(n),e=f(!1),k=q(()=>s.qty===0?u(0):u(s.qty*i.cart.price)),m=()=>{s.put(route("frontend.cart.update",i.cart.id),{preserveScroll:!0,onError:n=>{console.log(n)}})},b=n=>{_.delete(route("frontend.cart.delete",i.cart.id),{onStart:()=>e.value=!0,onError:c=>console.log(c),onSuccess:()=>g("deleted",!0),onFinish:()=>e.value=!1})},j=()=>{s.qty>1&&(s.qty--,m())},C=()=>{s.qty++,m()};return(n,c)=>(a(),r("div",M,[t("div",Q,[t("div",R,[t("div",H,[t("img",{class:"h-full w-full object-cover object-center",src:n.cart.image===null?o(z):n.cart.image,alt:n.cart.name},null,8,O)]),t("a",U,[d(h(n.cart.name)+" ",1),o(s).processing?(a(),r("span",W," updating... ")):p("",!0),e.value?(a(),r("span",Z," hapus item... ")):p("",!0),t("p",G,h(n.cart.shop),1)])]),t("div",J,[t("div",X,[Y,d(" "+h(u(n.cart.price)),1)]),t("div",tt,[t("button",{type:"button",class:"size-10 leading-10 text-gray-600 transition hover:opacity-75",onClick:j,disabled:o(s).processing||e.value}," − ",8,et),F(t("input",{type:"number","onUpdate:modelValue":c[0]||(c[0]=v=>o(s).qty=v),class:"h-10 w-10 border-transparent text-center [-moz-appearance:_textfield] sm:text-sm [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [&::-webkit-outer-spin-button]:appearance-none",disabled:o(s).processing||e.value,onKeyup:D(m,["enter"])},null,40,st),[[K,o(s).qty]]),t("button",{type:"button",class:"size-10 leading-10 text-gray-600 transition hover:opacity-75",onClick:C,disabled:o(s).processing||e.value}," + ",8,at)])]),t("div",ot,[t("div",nt,[rt,d(" "+h(k.value),1)]),t("div",it,[t("button",{type:"button",class:"p-1 bg-red-100 rounded-full",onClick:c[1]||(c[1]=v=>b(n.cart.id)),disabled:o(s).processing||e.value},[y(o(A),{class:"w-4 h-4 text-red-700"})],8,lt)])])])]))}}),dt={class:"lg:container p-4 min-h-[600px]"},ut={class:"lg:w-3/4 mx-auto space-y-4"},mt=t("h2",{class:"text-lg font-semibold text-gray-900 dark:text-white"}," Keranjang Belanja ",-1),pt={key:0,class:"space-y-2"},gt=t("div",null,[t("blockquote",{class:"my-6 border-l-2 pl-6 italic text-sm"},[t("strong",null,"Keterangan :"),d(" Silahkan cek kembali produk / jasa yang ingin dipesan melalui sistem ini, sebelum melakukan proses selesaikan pemesanan. ")])],-1),ht={class:"w-full space-x-2 text-center"},_t=["disabled"],bt={key:0},ft={key:1},yt=["disabled"],xt={key:0},kt={key:1},vt={key:1,class:"w-full"},wt=["src"],$t=t("h2",{class:"scroll-m-20 border-b pb-2 text-xl font-semibold tracking-tight transition-colors text-center"}," Tidak terdapat produk dalam keranjang belanja anda ",-1),jt=t("p",{class:"text-center text-muted-foreground"}," Silahkan tambahkan terlebih dahulu produk atau jasa yang kami sediakan di sistem. ",-1),Ct={key:2,class:"w-full"},St=["src"],qt=t("h2",{class:"scroll-m-20 border-b pb-2 text-xl font-semibold tracking-tight transition-colors text-center"}," Anda harus LogIn terlebih dahulu agar dapat melihat keranjang belanja anda. ",-1),Ft={class:"text-center text-muted-foreground"},Kt={layout:P},Ut=$({...Kt,__name:"Carts",props:{carts:{}},setup(x){const l=f(!1),i=f(!1),g=()=>{_.reload({only:["carts"]})},s=()=>{_.delete(route("frontend.cart.empty"),{onStart:()=>l.value=!0,onError:e=>console.log(e),onSuccess:()=>g(),onFinish:()=>l.value=!1})},u=()=>{_.post(route("frontend.cart.send"),{},{onStart:()=>i.value=!0,onError:e=>console.log(e),onFinish:()=>i.value=!1})};return(e,k)=>(a(),r(w,null,[y(o(N),{title:"Keranjang Belanja"}),t("div",dt,[t("div",ut,[mt,e.carts!==null&&e.carts.data.length>0?(a(),r("div",pt,[gt,(a(!0),r(w,null,E(e.carts.data,(m,b)=>(a(),T(ct,{key:b,cart:m,onDeleted:g},null,8,["cart"]))),128)),t("div",ht,[t("button",{type:"button",class:"mt-4 bg-orange-400 hover:bg-orange-600 focus:ring-3 focus:outline-none focus:ring-orang-300 font-medium rounded text-sm px-5 py-2.5 text-center text-white",disabled:i.value||l.value,onClick:s},[l.value?(a(),r("span",bt,"Mengosongkan Keranjang...")):(a(),r("span",ft,"Kosongkan Keranjang"))],8,_t),t("button",{onClick:u,type:"button",class:"mt-4 bg-blue-400 hover:bg-blue-600 focus:ring-3 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-5 py-2.5 text-center text-white",disabled:i.value||l.value},[i.value?(a(),r("span",xt,"Mengirim Pesanan...")):(a(),r("span",kt,"Kirim Pesanan"))],8,yt)])])):p("",!0),e.carts!==null&&e.carts.data.length===0?(a(),r("div",vt,[t("img",{src:o(L),alt:"empty_cart",class:"w-80 h-80 mx-auto"},null,8,wt),$t,jt])):p("",!0),e.carts===null?(a(),r("div",Ct,[t("img",{src:o(V),alt:"empty_cart",class:"w-80 h-80 mx-auto"},null,8,St),qt,t("p",Ft,[d(" Apabila belum punya akun silahkan mendaftar terlebih dahulu dengan mengklik tombol dibawah. "),y(o(I),{href:e.route("frontend.register"),as:"button",class:"mt-4 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-5 py-2.5 text-center"},{default:B(()=>[d(" Daftar Akun Baru ")]),_:1},8,["href"])])])):p("",!0)])])],64))}});export{Ut as default};
