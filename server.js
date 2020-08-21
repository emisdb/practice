/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
'use strict';
const http = require('http');
const fs = require('fs');
const html = fs.readFileSync('practice.html', 'utf8');
const server = http.createServer(function(request, response){
   response.end(html);
});
server.listen('3000');

