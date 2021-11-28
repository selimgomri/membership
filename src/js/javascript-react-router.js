import React from "react";
import { render } from "react-dom";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { App as NotifyComposer } from "./notify/forms/Composer";
import { NotFound } from "./views/NotFound";

const rootElement = document.getElementById("root");
render(
  <BrowserRouter>
    <Routes>
      <Route path="/notify/new/react" element={<NotifyComposer />}>
        {/* <Route path="expenses" element={<Expenses />} />
        <Route path="invoices" element={<Invoices />} /> */}
      </Route>
      <Route
        path="*"
        element={<NotFound />}
      />
    </Routes>
  </BrowserRouter>,
  rootElement
);
