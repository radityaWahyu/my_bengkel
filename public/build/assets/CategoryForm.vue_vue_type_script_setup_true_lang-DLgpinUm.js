import{_ as C,a as V,b as B,c as w,d as N}from"./DialogFooter.vue_vue_type_script_setup_true_lang-C2z3aiWO.js";import{_ as T}from"./DialogDescription.vue_vue_type_script_setup_true_lang-Ch6O-hB6.js";import{t as K,o as j,s as g,u as D,F as E,_ as z,a as I,b as L,c as M}from"./vee-validate-zod.esm-BuJycqYT.js";import{d as P,r as v,Q,F as b,o as i,e as q,w as t,b as e,u as a,f as u,t as k,a as A,n as G,m as H,c as f,i as J}from"./app-CB_Q1yd6.js";import{_ as y}from"./index-D4Ie_lJy.js";import{_ as O}from"./Input.vue_vue_type_script_setup_true_lang-D54lCQEL.js";const R={key:0,class:"text-xs text-red-500 font-medium"},U={key:0},W={key:1},sa=P({__name:"CategoryForm",props:{open:{type:Boolean,default:!0},title:{default:"Tambah Kategori"},category:{}},emits:["closed","saved"],setup(h,{emit:S}){var p;const l=h,m=S,F=K(j({name:g({message:"Data Kategori harus diisi"}).min(1,{message:"Data Kategori harus diisi."}),_token:g()})),o=v(!1),s=v(),c=D({validationSchema:F}),$=Q();c.setFieldValue("_token",$.props.csrf_token),c.setFieldValue("name",(p=l.category)==null?void 0:p.name);const x=()=>{s.value=null,l.category?window.history.back():m("closed",!0)},d=c.handleSubmit(r=>{l.category?b.put(route("backoffice.category.update",l.category.id),r,{preserveState:!0,onStart:()=>o.value=!0,onError:n=>{s.value=n},onSuccess:()=>{m("saved",!0)},onFinish:()=>o.value=!1}):b.post(route("backoffice.category.store"),r,{onStart:()=>o.value=!0,onError:n=>{s.value=n},onSuccess:()=>{m("saved",!0)},onFinish:()=>o.value=!1})});return(r,n)=>(i(),q(a(N),{open:r.open},{default:t(()=>[e(a(w),{class:"sm:max-w-[425px]"},{default:t(()=>[e(a(C),null,{default:t(()=>[e(a(V),null,{default:t(()=>[u(k(r.title),1)]),_:1}),e(a(T),{class:"text-sm"},{default:t(()=>[u(" Data kategori dipergunakan untuk memanajemen data produk atau jasa pada sistem. Silahkan menginputkan data dengan benar. ")]),_:1})]),_:1}),A("form",{onSubmit:n[0]||(n[0]=(..._)=>a(d)&&a(d)(..._))},[e(a(E),{name:"name"},{default:t(({componentField:_})=>[e(a(z),null,{default:t(()=>[e(a(I),{class:G({"text-red-500":s.value&&!!s.value.name})},{default:t(()=>[u("Nama Kategori")]),_:1},8,["class"]),e(a(L),null,{default:t(()=>[e(a(O),H({type:"text",placeholder:"Input kategori"},_,{class:[{"border border-red-500":s.value&&!!s.value.name},""],disabled:o.value}),null,16,["class","disabled"])]),_:2},1024),s.value?(i(),f("div",R,k(s.value.name),1)):J("",!0),e(a(M))]),_:2},1024)]),_:1})],32),e(a(B),null,{default:t(()=>[e(a(y),{type:"button",variant:"ghost",form:"dialogForm",onClick:x,disabled:o.value},{default:t(()=>[u(" Batal ")]),_:1},8,["disabled"]),e(a(y),{type:"submit",onClick:a(d),disabled:o.value},{default:t(()=>[o.value?(i(),f("span",U,"Menyimpan data...")):(i(),f("span",W,"Simpan"))]),_:1},8,["onClick","disabled"])]),_:1})]),_:1})]),_:1},8,["open"]))}});export{sa as _};
