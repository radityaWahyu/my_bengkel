import{_ as B}from"./Checkbox.vue_vue_type_script_setup_true_lang-BiAqCv97.js";import{_ as v}from"./index-D4Ie_lJy.js";import{_ as R}from"./Input.vue_vue_type_script_setup_true_lang-D54lCQEL.js";import{_ as q,a as W}from"./index-Dao5mdM1.js";import{_ as Z}from"./index-CoOCKyWx.js";import{d as P,r as f,o as x,c as $,b as o,w as r,u as n,f as g,a as c,t as A,y as G,j as V,e as J,g as Q,h as l,F as k,Z as X}from"./app-CB_Q1yd6.js";import{_ as Y}from"./DataTable.vue_vue_type_script_setup_true_lang-bC7ddmBa.js";import{_ as ee,a as ae,b as te,c as se,d as le,e as oe,f as ne,g as ie}from"./AlertDialogCancel.vue_vue_type_script_setup_true_lang-BM7P7fmY.js";import{P as re,A as b,a as T}from"./pen-line-B_HVBMoN.js";import{T as U}from"./trash-CN3VjTEs.js";import{_ as ce}from"./ComboBox.vue_vue_type_script_setup_true_lang-K26lVO0Q.js";import{_ as pe}from"./ConfirmDialog.vue_vue_type_script_setup_true_lang-D8waPHtR.js";import{B as ue}from"./Backoffice-B6AXC8lO.js";import{P as me}from"./plus-BA36iQ6u.js";import{w as de}from"./index-C2XJz47D.js";import"./index-CfPuhHd9.js";import"./utils--eAPk3wG.js";import"./bundle-mjs-Dk7hgz7r.js";import"./CheckIcon-CGfPqg5k.js";import"./shadcn-BWvbMO9Q.js";/* empty css            */import"./Skeleton.vue_vue_type_script_setup_true_lang-D3f5crE8.js";import"./AlertTitle.vue_vue_type_script_setup_true_lang-CYwTo7kE.js";import"./TableRow.vue_vue_type_script_setup_true_lang-DO3giS2u.js";import"./octagon-alert-BvysA4bE.js";import"./createLucideIcon-BsoM3zrU.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./store-ct7EYkSP.js";import"./circle-user-Dao3zDS1.js";import"./badge-info-CC5SfLbS.js";import"./triangle-alert-DxY52lw5.js";const fe={class:"space-x-2 w-15"},ve=P({__name:"ButtonAction",props:{id:{type:String,required:!0}},emits:["deleted","updated"],setup(y,{emit:w}){const d=w,i=f(!1);return(h,t)=>(x(),$("div",fe,[o(n(v),{type:"button",variant:"secondary",size:"icon",onClick:t[0]||(t[0]=u=>d("updated",y.id))},{default:r(()=>[o(n(re),{class:"w-4 h-4 text-blue-600"})]),_:1}),o(n(v),{type:"button",variant:"secondary",size:"icon",onClick:t[1]||(t[1]=u=>i.value=!0)},{default:r(()=>[o(n(U),{class:"w-4 h-4 text-tomato"})]),_:1}),o(n(ie),{open:i.value},{default:r(()=>[o(n(ee),null,{default:r(()=>[o(n(ae),null,{default:r(()=>[o(n(te),null,{default:r(()=>[g(" Apakah anda ingin menghapus data ini? ")]),_:1}),o(n(se),null,{default:r(()=>[g(" Aksi ini akan menghapus data permanen dari sistem dan tidak bisa di kembalikan. ")]),_:1})]),_:1}),o(n(le),null,{default:r(()=>[o(n(oe),{onClick:t[2]||(t[2]=u=>i.value=!1),class:"border-none"},{default:r(()=>[g(" Batalkan ")]),_:1}),o(n(ne),{onClick:t[3]||(t[3]=u=>d("deleted",y.id)),class:"bg-tomato"},{default:r(()=>[g(" Hapus Data ")]),_:1})]),_:1})]),_:1})]),_:1},8,["open"])]))}}),ge={class:"flex items-center gap-3"},_e=G('<div class="relative size-min"><div class="relative box-content flex items-center justify-center overflow-hidden rounded-full size-8 bg-blue-700 stroke-white"><div class="flex items-center justify-center size-3.5"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="stroke-inherit"><path d="M16.6668 17.5V15.8333C16.6668 14.9492 16.3157 14.1014 15.6905 13.4763C15.0654 12.8512 14.2176 12.5 13.3335 12.5H6.66683C5.78277 12.5 4.93493 12.8512 4.3098 13.4763C3.68469 14.1014 3.3335 14.9492 3.3335 15.8333V17.5"></path><path d="M9.99984 10.0002C11.8408 10.0002 13.3332 8.50775 13.3332 6.66683C13.3332 4.82588 11.8408 3.3335 9.99984 3.3335C8.15889 3.3335 6.6665 4.82588 6.6665 6.66683C6.6665 8.50775 8.15889 10.0002 9.99984 10.0002Z"></path></svg></div></div></div>',1),he={class:"font-semibold text-sm capitalize"},ye=P({__name:"EmployeeNameBox",props:{employee:{}},setup(y){return(w,d)=>(x(),$("div",ge,[_e,c("div",null,[c("h3",he,A(w.employee.name),1)])]))}}),we={class:"space-y-2 mx-auto w-full"},ke={class:"space-y-2"},xe={class:"flex items-center justify-between"},be=c("h1",{class:"text-lg font-semibold md:text-xl"},"Pegawai",-1),Te={class:"flex gap-2"},$e={key:0,class:"flex gap-2"},Ce=c("strong",null,"+ tambah",-1),Pe={class:"w-full"},Ne={class:"flex items-center justify-between py-4"},Se={class:"inline-flex items-center gap-3"},je=c("p",{class:"text-xs font-medium"},"Data Perpage :",-1),De={layout:ue},ca=P({...De,__name:"Index",props:{employees:Object,params:Object,employee:{required:!1,type:Object}},setup(y){var N,S,j,D;const w=f([{value:10,label:10},{value:25,label:25},{value:50,label:50},{value:100,label:100}]),d=f(!1),i=y,h=f((N=i.params)==null?void 0:N.search),t=f({sortName:(S=i.params)==null?void 0:S.sortName,sortType:(j=i.params)==null?void 0:j.sortType,perPage:(D=i.params)==null?void 0:D.perPage}),u=f({open:!1,cancelText:"Batalkan",okText:"Hapus Data"}),C=f(null),z=[{id:"select",header:({table:e})=>l(B,{checked:e.getIsAllPageRowsSelected()||e.getIsSomePageRowsSelected()&&"indeterminate","onUpdate:checked":a=>{console.log(a),a?e.getRowModel().rows.forEach(M=>{m.value.push(M.original.id)}):(m.value=[],e.resetRowSelection()),e.toggleAllPageRowsSelected(!!a)},ariaLabel:"Select all"}),cell:({row:e})=>l(B,{class:"mr-5",id:"check",checked:e.getIsSelected(),"onUpdate:checked":a=>{a?m.value.push(e.original.id):m.value=m.value.filter(s=>s!==e.original.id),e.toggleSelected(!!a)},ariaLabel:"Select row"}),enableSorting:!1,enableHiding:!1},{accessorKey:"name",header:({column:e})=>l(v,{variant:"ghost",onClick:()=>{t.value.sortName="name",t.value.sortType=="asc"?t.value.sortType="desc":t.value.sortType="asc",_(p.value.current_page)},class:"w-full flex justify-between text-left px-0"},()=>{var a,s;return[l("div",{class:"gap-2 flex items-center "},[((a=i.params)==null?void 0:a.sortType)=="desc"&&((s=i.params)==null?void 0:s.sortName)=="name"?l(b,{class:"h-4 w-4"}):l(T,{class:"h-4 w-4"}),"Nama Pegawai"])]}),cell:({row:e})=>l(ye,{employee:e.original})},{accessorKey:"phone",header:({column:e})=>l(v,{variant:"ghost",onClick:()=>{t.value.sortName="phone",t.value.sortType=="asc"?t.value.sortType="desc":t.value.sortType="asc",_(p.value.current_page)},class:"w-full flex justify-between text-left px-0"},()=>{var a,s;return[l("div",{class:"gap-2 flex items-center "},[((a=i.params)==null?void 0:a.sortType)=="desc"&&((s=i.params)==null?void 0:s.sortName)=="name"?l(b,{class:"h-4 w-4"}):l(T,{class:"h-4 w-4"}),"No Telepon"])]}),cell:({row:e})=>l("div",{},e.original.phone)},{accessorKey:"whatsapp",header:({column:e})=>l(v,{variant:"ghost",onClick:()=>{t.value.sortName="whatsapp",t.value.sortType=="asc"?t.value.sortType="desc":t.value.sortType="asc",_(p.value.current_page)},class:"w-full flex justify-between text-left px-0"},()=>{var a,s;return[l("div",{class:"gap-2 flex items-center "},[((a=i.params)==null?void 0:a.sortType)=="desc"&&((s=i.params)==null?void 0:s.sortName)=="name"?l(b,{class:"h-4 w-4"}):l(T,{class:"h-4 w-4"}),"No Whatsapp"])]}),cell:({row:e})=>l("div",{},e.original.whatsapp)},{accessorKey:"shop",header:({column:e})=>l(v,{variant:"ghost",onClick:()=>{t.value.sortName="shop",t.value.sortType=="asc"?t.value.sortType="desc":t.value.sortType="asc",_(p.value.current_page)},class:"w-full flex justify-between text-left px-0"},()=>{var a,s;return[l("div",{class:"gap-2 flex items-center "},[((a=i.params)==null?void 0:a.sortType)=="desc"&&((s=i.params)==null?void 0:s.sortName)=="name"?l(b,{class:"h-4 w-4"}):l(T,{class:"h-4 w-4"}),"Unit Layanan"])]}),cell:({row:e})=>l(Z,{variant:"secondary"},e.original.shop)},{id:"actions",enableHiding:!1,cell:({row:e})=>{const a=e.original.id;return l("div",{class:"relative text-center"},l(ve,{id:a,onDeleted:s=>F(s),onUpdated:s=>k.get(route("backoffice.employee.edit",s))}))}}],m=f([]),E=V(()=>{var e;return(e=i.employees)==null?void 0:e.data}),p=V(()=>{var e;return(e=i.employees)==null?void 0:e.meta}),O=e=>{const a=f({page:e,perPage:t.value.perPage});return t.value.sortName!==null&&t.value.sortType!==null&&Object.assign(a.value,{sortName:t.value.sortName,sortType:t.value.sortType}),h!==null&&Object.assign(a.value,{search:h}),a},_=e=>{const a=O(e);k.get(route("backoffice.employee.index"),a.value,{only:["employees","params"],preserveState:!0,preserveScroll:!0,onStart:()=>d.value=!0,onFinish:()=>d.value=!1})},F=e=>{k.delete(route("backoffice.employee.delete",e),{onStart:()=>d.value=!0,onFinish:()=>d.value=!1})},H=e=>_(e),L=()=>_(1),I=()=>{k.post(route("backoffice.employee.delete-all"),{ids:m.value},{onFinish:()=>{var e;m.value=[],(e=C.value)==null||e.resetTable(),u.value.open=!1}})},K=()=>{var e;m.value=[],(e=C.value)==null||e.resetTable()};return de(h,()=>{_(p.value.current_page)},{debounce:500,maxWait:1e3}),(e,a)=>(x(),$(Q,null,[o(n(X),{title:"Data Pegawai"}),c("div",we,[c("div",ke,[c("div",xe,[be,c("div",Te,[m.value.length>0?(x(),$("div",$e,[o(n(v),{variant:"secondary",class:"flex items-center gap-2",onClick:K},{default:r(()=>[g(" Batalkan ")]),_:1}),o(n(v),{variant:"destructive",class:"flex items-center gap-2 font-semibold",size:"icon",onClick:a[0]||(a[0]=s=>u.value.open=!0)},{default:r(()=>[o(n(U),{class:"w-4 h-4","stroke-width":"3px"})]),_:1})])):(x(),J(n(v),{key:1,variant:"default",onClick:a[1]||(a[1]=s=>n(k).get(e.route("backoffice.employee.create"))),class:"flex items-center gap-2 font-semibold"},{default:r(()=>[o(n(me),{class:"w-4 h-4","stroke-width":"3px"}),g(" Tambah Pegawai ")]),_:1}))])]),o(n(W),{class:"bg-secondary shadow-inner shadow-gray-100 max-w-2xl"},{default:r(()=>[o(n(q),{class:"text-xs"},{default:r(()=>[g(" Halaman untuk memanajemen Pegawai yang terdapat pada setiap Unit Layanan di SMKN 1 Purwosari. Untuk menambah data baru silahkan mengklik tombol "),Ce]),_:1})]),_:1})]),c("div",Pe,[c("div",Ne,[o(n(R),{placeholder:"Cari data...",modelValue:h.value,"onUpdate:modelValue":a[2]||(a[2]=s=>h.value=s),class:"w-1/2 bg-white"},null,8,["modelValue"]),c("div",Se,[je,o(ce,{lists:w.value,modelValue:t.value.perPage,"onUpdate:modelValue":a[3]||(a[3]=s=>t.value.perPage=s),onChange:L,class:"w-100"},null,8,["lists","modelValue"])])]),o(Y,{ref_key:"employeesTable",ref:C,columns:z,data:E.value,"current-page":p.value.current_page,"last-page":p.value.last_page,"prev-page":p.value.current_page,"next-page":p.value.to,total:p.value.total,loading:d.value,"per-page":p.value.per_page,onChangePage:H},null,8,["data","current-page","last-page","prev-page","next-page","total","loading","per-page"])]),o(pe,{open:u.value.open,"cancel-text":u.value.cancelText,"ok-text":u.value.okText,onCancel:a[4]||(a[4]=s=>u.value.open=!1),onOk:I},{title:r(()=>[g(" Apakah anda ingin menghapus "+A(m.value.length)+" Data? ",1)]),description:r(()=>[g(" Data akan dihapus secara permanen dari sistem dan tidak bisa di kembalikan, mohon untuk mengecek kembali data yang akan dihapus. ")]),_:1},8,["open","cancel-text","ok-text"])])],64))}});export{ca as default};
