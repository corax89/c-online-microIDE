var out          = document.getElementById('output');
var btnCompile   = document.getElementById('compile');
var btnHello     = document.getElementById('hello');
var setCompiler  = document.getElementById("setcompiler");
var optimization = document.getElementById("optimization");
var codePage    = document.getElementById("code_page");
var programmLink = document.getElementById("link");

btnCompile.addEventListener("click", sendToCompile);
btnHello.addEventListener("click", loadHello);

function sendToCompile(){
    //собираем тело запроса
    var body = 'setcompiler=' + encodeURIComponent(setCompiler.value) 
         +'&optimization=' + encodeURIComponent(optimization.value)
         +'&code_page=' + encodeURIComponent(codePage.value)
         +'&text_box=' + encodeURIComponent(editAreaLoader.getValue("codebox"));
    //создаем запрос
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'compile.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    //отправляем запрос
    xhr.send(body); // (1)
    xhr.onreadystatechange = function() { // (3)
        if (xhr.readyState != 4) 
            return;
        //активируем кнопку компиляции
        btnCompile.disabled = false;
        if (xhr.status != 200) {
            out.value = 'server error ' + xhr.status + ': ' + xhr.statusText;
        } 
        else {
            out.value = xhr.responseText;
            //меняем ссылку на программу в зависимости от выбранного компилятора
            if (setCompiler.value == 0)
                programmLink.href = 'tmp/program';
            else
                programmLink.href = 'tmp/program.exe';
        }
    }
    //блокируем кнопки в ожидании ответа
    out.value = 'Компиляция...'; // (2)
    btnCompile.disabled = true;
    programmLink.href = '#';
}

function loadHello(){
    //загружаем в редактор текст программы Hello world
    var s='#include <stdio.h>\n\n'
    +'int main (void){\n'
    +'\tprintf ("Hello, World!\\n");\n'
    +'\tgetchar ();\n\treturn 0;\n}\n';
    editAreaLoader.setValue("codebox", s);
}