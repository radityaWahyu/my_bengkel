import{d as l,C as _,o as n,c as d,b as p,u as m,Z as h,a as t,t as s,g as r,p as g,D as x,E as y}from"./app-CB_Q1yd6.js";import{_ as u}from"./_plugin-vue_export-helper-DlAUqK2U.js";/* empty css            */const e=a=>(x("data-v-28b9c7d1"),a=a(),y(),a),b={class:"sheet-outer A4 bg-gray-100 print:bg-white py-5 print:py-0"},f={class:"py-8 space-y-10 sheet padding-5mm"},v={class:"flex items-center justify-between pb-10"},I=e(()=>t("div",{class:"divide-y divide-gray-300"},[t("div",{class:"text-3xl font-semibold text-tomato"}," APLIKASI MY UPJ "),t("div",{class:"text-gray-500 font-medium text-sm"}," SMKN 1 PURWOSARI KAB PASURUAN ")],-1)),w={class:"divide-y divide-gray-300 text-right"},A=e(()=>t("div",{class:"font-semibold text-tomato"},"INVOICE",-1)),S={class:"text-gray-500 font-medium text-lg"},N={class:"grid grid-cols-2"},P=e(()=>t("h4",{class:"font-semibold"},"DITERBITKAN ATAS NAMA",-1)),T={class:"grid grid-cols-[30%_70%] items-center"},D=e(()=>t("p",{class:"text-sm"},"Penjual",-1)),U={class:"font-semibold"},k=e(()=>t("h4",{class:"font-semibold"},"UNTUK",-1)),B={class:"grid grid-cols-[30%_70%] items-center"},R=e(()=>t("p",{class:"text-sm"},"Pembeli",-1)),K={class:"font-medium capitalize"},C={class:"grid grid-cols-[30%_70%] items-center"},E=e(()=>t("p",{class:"text-sm"},"Tanggal Pesan",-1)),M={class:"font-medium"},F={class:"grid grid-cols-[30%_70%] items-center"},L=e(()=>t("p",{class:"text-sm"},"Tanggal Dibayar",-1)),V={class:"font-medium"},j={class:"relative overflow-x-auto"},J={class:"w-full text-gray-500 dark:text-gray-400"},O=e(()=>t("thead",{class:"text-xs text-gray-900 uppercase bg-gray-50"},[t("tr",{class:"border-y-2 border-gray-500"},[t("th",{scope:"col",class:"px-2 py-3 text-left"}," Nama Item "),t("th",{scope:"col",class:"px-2 py-3 text-right"}," Harga Satuan "),t("th",{scope:"col",class:"px-2 py-3 text-center"}," Jumlah "),t("th",{scope:"col",class:"px-2 py-3 text-right"}," Sub Total ")])],-1)),q={class:"bg-white border-b dark:bg-gray-800 dark:border-gray-700 text-gray-900"},z={scope:"row",class:"px-2 py-2 font-medium whitespace-nowrap text-left text-sm"},H={class:"px-2 py-2 text-right text-sm"},W={class:"px-2 py-2 text-center"},Y={class:"px-2 py-2 text-right font-medium text-sm"},Z={class:"bg-white border-b"},G=e(()=>t("td",{colspan:"3",class:"px-2 py-2 text-sm font-semibold text-right text-gray-800"}," Total Bayar ",-1)),Q={colspan:"3",class:"px-2 py-2 font-medium text-right text-gray-800 text-sm"},X={class:"bg-white border-b"},$=e(()=>t("td",{colspan:"3",class:"px-2 py-2 text-sm font-semibold text-right text-gray-800"}," Status Pembayaran ",-1)),tt={colspan:"3",class:"px-2 py-2 font-semibold text-right text-tomato text-sm"},st=l({__name:"Invoices",props:{transaction:{}},setup(a){_(()=>{window.print()});const c=o=>new Intl.NumberFormat("en-ID",{style:"currency",currency:"IDR",maximumFractionDigits:0}).format(o);return(o,et)=>(n(),d(r,null,[p(m(h),{title:"Cetak Invoice"}),t("div",b,[t("div",f,[t("div",v,[I,t("div",w,[A,t("div",S,s(o.transaction.transaction_code),1)])]),t("div",N,[t("div",null,[P,t("div",T,[D,t("p",U,": "+s(o.transaction.shop),1)])]),t("div",null,[k,t("div",B,[R,t("p",K," : "+s(o.transaction.customer_name),1)]),t("div",C,[E,t("p",M," : "+s(o.transaction.created_at),1)]),t("div",F,[L,t("p",V," : "+s(o.transaction.finished_at),1)])])]),t("div",j,[t("table",J,[O,t("tbody",null,[(n(!0),d(r,null,g(o.transaction.details,(i,ot)=>(n(),d("tr",q,[t("th",z,s(i.name),1),t("td",H,s(c(i.price)),1),t("td",W,s(i.quantity),1),t("td",Y,s(c(i.total)),1)]))),256)),t("tr",Z,[G,t("td",Q,s(c(o.transaction.total)),1)]),t("tr",X,[$,t("td",tt,s(o.transaction.finished_at!==null?"LUNAS":"-"),1)])])])])])])],64))}}),nt=u(st,[["__scopeId","data-v-28b9c7d1"]]);export{nt as default};
