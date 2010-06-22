var dmMarkitupMarkdown = typeof dmMarkitupMarkdown == 'undefined' ? {
  previewParserPath:  '',
  onShiftEnter:    {keepDefault:false, openWith:'\n\n'},
  markupSet: [
//    {name:'First Level Heading', key:'1', placeHolder:'Your title here...', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '=') } },
    {name:'Heading 2', className: 'markitup_heading_2', key:'2', openWith:'## ', placeHolder:'Your title here...' },
    {name:'Heading 3', className: 'markitup_heading_3', key:'3', openWith:'### ', placeHolder:'Your title here...' },
    {name:'Heading 4', className: 'markitup_heading_4', key:'4', openWith:'#### ', placeHolder:'Your title here...' },
    {separator:'---------------' },
    {name:'Bold', className: 'markitup_bold', key:'B', openWith:'**', closeWith:'**'},
    {name:'Italic', className: 'markitup_italic', key:'I', openWith:'_', closeWith:'_'},
    {separator:'---------------' },
    {name:'Bulleted List', className: 'markitup_ul', openWith:'- ' },
    {name:'Numeric List', className: 'markitup_ol', openWith:function(markItUp) {
      return markItUp.line+'. ';
    }},
    {separator:'---------------' },
    {name:'Link', className: 'markitup_link', key:'L', openWith:'[', closeWith:']([![Url:!:http://]!] "[![Title]!]")', placeHolder:'Your text to link here...' }
//    {separator:'---------------'},
//    {name:'Quotes', className: 'markitup_quote', openWith:'> '}
  ],
  resizeHandle:    false
} : dmMarkitupMarkdown;

// mIu nameSpace to avoid conflict.
miu = {
  markdownTitle: function(markItUp, character) {
    heading = '';
    n = $.trim(markItUp.selection||markItUp.placeHolder).length;
    for(i = 0; i < n; i++) {
      heading += character;
    }
    return '\n'+heading;
  }
}
