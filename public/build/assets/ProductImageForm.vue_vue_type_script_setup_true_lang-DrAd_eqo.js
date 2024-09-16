import{_ as E,a as I,b as N,c as D,d as J}from"./DialogFooter.vue_vue_type_script_setup_true_lang-C2z3aiWO.js";import{u as z,F as b,_ as h,a as k,b as y,c as x,t as M,o as P,s as j,d as A}from"./vee-validate-zod.esm-BuJycqYT.js";import{d as L,Q,T as X,o as l,e as $,w as t,b as s,u as e,f as m,a as Z,n as F,m as q,c as d,t as C,i as _}from"./app-CB_Q1yd6.js";import{_ as S}from"./index-D4Ie_lJy.js";import{_ as H}from"./Input.vue_vue_type_script_setup_true_lang-D54lCQEL.js";import{_ as K,a as O}from"./InputImage.vue_vue_type_script_setup_true_lang-mCaQgNwp.js";const R={key:0,class:"text-xs text-red-500 font-medium"},W={key:0,class:"text-xs text-red-500 font-medium"},Y={key:0},ee={key:1},ae=1e6,me=L({__name:"ProductImageForm",props:{id:{},open:{type:Boolean}},emits:["closed","saved"],setup(V,{emit:v}){const B=V,p=v,T=Q(),G=o=>{if((o==null?void 0:o.size)<=ae)return!0},U=M(P({title:j({message:"Judul Gambar harus diisi"}).min(1,{message:"Judul Gambar harus diisi."}),image:A().refine(o=>o,"Gambar harus diisi").refine(o=>G(o),"Ukuran gambar maksimal 1MB.")})),f=z({validationSchema:U}),a=X({title:"",_token:T.props.csrf_token,image:""}),w=()=>{a.clearErrors(),f.resetForm,p("closed",!0)},c=f.handleSubmit(o=>{a.post(route("backoffice.product.images.store",B.id),{forceFormData:!0,onSuccess:()=>{p("saved",!0)}})});return(o,r)=>(l(),$(e(J),{open:o.open},{default:t(()=>[s(e(D),{class:"sm:max-w-[425px]"},{default:t(()=>{var g;return[s(e(E),null,{default:t(()=>[s(e(I),{class:"font-medium"},{default:t(()=>[m("Tambah Gambar")]),_:1})]),_:1}),Z("form",{onSubmit:r[2]||(r[2]=(...n)=>e(c)&&e(c)(...n)),class:"space-y-2"},[s(e(b),{name:"title"},{default:t(({componentField:n})=>[s(e(h),null,{default:t(()=>[s(e(k),{class:F({"text-red-500":e(a).errors.title})},{default:t(()=>[m("Judul Gambar")]),_:1},8,["class"]),s(e(y),null,{default:t(()=>[s(e(H),q({type:"text",placeholder:"Input nama unit"},n,{modelValue:e(a).title,"onUpdate:modelValue":r[0]||(r[0]=u=>e(a).title=u),class:{"border border-red-500":e(a).errors.title},disabled:e(a).processing}),null,16,["modelValue","class","disabled"])]),_:2},1024),e(a).errors.title?(l(),d("div",R,C(e(a).errors.title),1)):_("",!0),s(e(x))]),_:2},1024)]),_:1}),s(e(b),{name:"image"},{default:t(({componentField:n,field:u})=>[s(e(h),null,{default:t(()=>[s(e(k),{class:F({"text-red-500":e(a).errors.image})},{default:t(()=>[m("Gambar")]),_:1},8,["class"]),s(e(y),null,{default:t(()=>[s(K,{class:"h-52",modelValue:e(a).image,"onUpdate:modelValue":r[1]||(r[1]=i=>e(a).image=i),onChange:i=>{e(a).image=i,u.onChange(i)},loading:e(a).processing},null,8,["modelValue","onChange","loading"])]),_:2},1024),e(a).errors.image?(l(),d("div",W,C(e(a).errors.image),1)):_("",!0),s(e(x))]),_:2},1024)]),_:1}),e(a).progress?(l(),$(e(O),{key:0,value:(g=e(a).progress)==null?void 0:g.percentage,max:100,class:"w-full"},null,8,["value"])):_("",!0)],32),s(e(N),null,{default:t(()=>[s(e(S),{type:"button",variant:"ghost",form:"dialogForm",onClick:w,disabled:e(a).processing},{default:t(()=>[m(" Batal ")]),_:1},8,["disabled"]),s(e(S),{type:"submit",onClick:e(c),disabled:e(a).processing},{default:t(()=>[e(a).processing?(l(),d("span",Y,"Upload gambar...")):(l(),d("span",ee,"Upload"))]),_:1},8,["onClick","disabled"])]),_:1})]}),_:1})]),_:1},8,["open"]))}});export{me as _};
