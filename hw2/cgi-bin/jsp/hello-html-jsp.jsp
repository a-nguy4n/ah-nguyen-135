<!-- package hw2.cgi-bin.jsp;

public class hello-html-jsp {
    
} -->

<%@ page contentType="text/html; charset=UTF-8" %>
<%
    response.setHeader("Cache-Control", "no-cache");

    java.util.Date date = new java.util.Date();
    String ip = request.getRemoteAddr();
%>

<!DOCTYPE html>
<html>
<head>
  <title>Hello From Allison & Haley! (JSP HMTL)</title>
</head>
<body>

  <h1 style="text-align:center;">Welcome to our JSP-HTML Page</h1>
  <hr/>

  <p>Welcome, Bienvenido, Willkommen</p>
  <p>This page was generated with the JSP programming language</p>

  <p>This program was generated at: <%= date.toString() %></p>
  <p>Your current IP Address is: <%= ip %></p>

</body>
</html>
