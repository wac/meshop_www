function clipboardTextarea(i)
{
    i.focus();
    i.select();
    CopiedTxt=document.selection.createRange();
    CopiedTxt.execCommand("Copy");
}
