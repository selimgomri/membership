import React from "react";
import { render } from "react-dom";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { Composer as NotifyComposer } from "./notify/forms/Composer";
import { Success as NotifySuccess } from "./notify/forms/Success";
import { NotFound } from "./views/NotFound";

const rootElement = document.getElementById("root");
render(
  <BrowserRouter>
    <Routes>
      <Route path="/notify/new" element={<NotifyComposer />}>
        {/* <Route path="expenses" element={<Expenses />} />
        <Route path="invoices" element={<Invoices />} /> */}
      </Route>
      <Route path="/notify/new/success" element={<NotifySuccess />}>
      </Route>
      <Route
        path="*"
        element={<NotFound />}
      />
    </Routes>
  </BrowserRouter>,
  rootElement
);
